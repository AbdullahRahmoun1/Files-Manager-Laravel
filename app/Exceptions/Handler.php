<?php

namespace App\Exceptions;

use App\Traits\Logger;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Str;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    use Logger;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {
//        return parent::render($request,$e);
        //Our exceptions
        if ($e instanceof BaseException) {
            return $e->render();
        }
        //Laravel's Exceptions but we want to handle them differently
        if ($e instanceof ModelNotFoundException) {
            return $this->renderModelNotFound($e);
        }
        if ($e instanceof ValidationException) {
            return $this->renderValidationError($e);
        }

        //unknown exception!
        if (env('APP_DEBUG', true)) {
            return $this->convertToCorrectFormat(
                parent::render($request, $e)
            );
        }else {
            return $this->errorResponse(
                __('custom.Unexpected error'),
                500
            );
        }
    }

    private function renderModelNotFound(ModelNotFoundException $e)
    {
        $modelClass = $e->getModel();
        // Convert the model class name to a more user-friendly name
        $modelName = class_basename($modelClass);
        $modelName = str_replace('_', ' ', Str::snake($modelName));
        $modelName = ucfirst($modelName);
        $message = "Sorry, the requested " . $modelName . " could not be found.";
        return $this->errorResponse($message,404);
    }

    private function renderValidationError(ValidationException $e) {
        $response = [
            'message' => $e->getMessage(),
            'errors' => collect($e->errors())->flatten(),
        ];
        return response()->json($response, $e->status);
    }

    private function errorResponse($message,$code){
        return response()->json([
            'message' => $message,
            'errors' => [
                $message
            ]
        ], $code);
    }

    public function report(Throwable $e)
    {
        if ($e instanceof QueryException){
            $this->log_exception($e,[
                'sql' => PHP_EOL.$e->getSql(),
                'bindings' =>[PHP_EOL]+$e->getBindings(),
            ],'database');
        }
        if ($e instanceof BaseException) {
            $e->report();
        }
        parent::report($e);
    }

    private function convertToCorrectFormat(Response $response) :Response{
        $data=$response->getContent();
        $data = json_decode($data,true);
        // dd($response);
        $result = [
            'message'=>$data['message']??"",
            'errors'=>isset($data['errors'])?$data['errors']:[$data['message']],
            'stacktrace' => $data['trace']
        ];
        $response->setContent(json_encode($result));
        return $response;
    }
}
