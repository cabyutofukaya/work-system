<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

/**
 * 強制的にJSONをリクエスト
 * リクエストヘッダに Accept: application/json を付与する
 *
 * @link https://stackoverflow.com/questions/46035072/enforce-json-middleware
 * @link https://stackoverflow.com/questions/36366727/how-do-you-force-a-json-response-on-every-response-in-laravel
 */
class ForceJsonResponse
{
    public function handle($request, Closure $next)
    {
        // リクエストヘッダに Accept:application/json を加える
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}