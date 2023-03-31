<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * apache風のアクセスログを出力するミドルウェア
 * Class RequestLogger
 * @package App\Http\Middleware
 */
class RequestLogger
{
    private $start;
    private $end;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 開始時刻
        $this->start = microtime(true);

        return $next($request);
    }

    public function terminate($request, $response)
    {
        // 終了時刻
        $this->end = microtime(true);

        // ローカル環境以外ではログを保存
        if (!App::environment('local')) {
            $this->log($request, $response);
        }
    }

    protected function log($request, $response)
    {
        // apache combined風のログに実行時間msを追加したもの
        $message = sprintf(
            '%s - %s "%s %s %s" %s %sbytes %sms "%s" "%s"',
            $request->getClientIp(),
            $request->getUser() ?? "-",
            strtoupper($request->getMethod()),
            $request->getRequestUri(),
            $request->getProtocolVersion(),
            $response->getStatusCode(),
            strlen(bin2hex($response->getContent())) / 2,
            round(($this->end - $this->start) * 1000),
            $request->server('HTTP_REFERER'),
            $request->server('HTTP_USER_AGENT')
        );

        // ログ出力
        Log::channel('accesslog')->info($message);
    }
}