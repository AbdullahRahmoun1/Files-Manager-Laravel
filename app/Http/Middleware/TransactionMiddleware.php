<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TransactionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            DB::beginTransaction();
            $response =  $next($request);
        }catch(Exception $e){
            DB::rollBack();
            throw new \Wever\Laradot\App\Exceptions\GeneralException($e->getMessage(),500);
        }
        if($response instanceof Response && $response->getStatusCode() > 399){
            DB::rollBack();
        }else {
            DB::commit();
        }
        return $response;
    }
}
