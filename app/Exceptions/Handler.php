<?php

namespace App\Exceptions;

use App\Traits\Logger;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Str;
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e): Response
    {
        // Handle custom exceptions
        if ($e instanceof BaseException) {
            return $e->render();
        }

        // Handle Laravel's specific exceptions
        if ($e instanceof ModelNotFoundException) {
            return $this->renderModelNotFound($e);
        }

        if ($e instanceof ValidationException) {
            return $this->renderValidationError($e);
        }

        // Handle unknown exceptions
        if (config('app.debug')) {
            return $this->convertToCorrectFormat(
                parent::render($request, $e)
            );
        } else {
            return $this->errorResponse(
                __('custom.Unexpected error'),
                500
            );
        }
    }

    /**
     * Render a model not found exception.
     *
     * @param ModelNotFoundException $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderModelNotFound(ModelNotFoundException $e)
    {
        $modelClass = $e->getModel();
        $modelName = class_basename($modelClass);
        $modelName = str_replace('_', ' ', Str::snake($modelName));
        $modelName = ucfirst($modelName);
        $message = "Sorry, the requested " . $modelName . " could not be found.";
        return $this->errorResponse($message, 404);
    }

    /**
     * Render a validation exception.
     *
     * @param ValidationException $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderValidationError(ValidationException $e)
    {
        $response = [
            'message' => $e->getMessage(),
            'errors' => collect($e->errors())->flatten(),
        ];
        return response()->json($response, $e->status);
    }

    /**
     * Return a JSON error response.
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    private function errorResponse(string $message, int $code)
    {
        return response()->json([
            'message' => $message,
            'errors' => [$message],
        ], $code);
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     */
    public function report(Throwable $e): void
    {
        if ($e instanceof QueryException) {
            $this->log_exception($e, [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ], 'database');
        }

        if ($e instanceof BaseException) {
            $e->report();
        }

        parent::report($e);
    }

    /**
     * Convert a Laravel response to a consistent JSON format.
     *
     * @param Response $response
     * @return Response
     */
    private function convertToCorrectFormat(Response $response): Response
    {
        $data = $response->getContent();

        $decoded = json_decode($data, true);

        // Handle cases where the response content might not be JSON
        if (!is_array($decoded)) {
            $decoded = ['message' => $data, 'trace' => []];
        }

        $result = [
            'message' => $decoded['message'] ?? '',
            'errors' => $decoded['errors'] ?? [$decoded['message'] ?? 'Unexpected error'],
            'stacktrace' => $decoded['trace'] ?? [],
        ];

        $response->setContent(json_encode($result));
        return $response;
    }
}
