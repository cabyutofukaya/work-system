<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StaticController extends Controller
{
    /**
     * スタティックファイル出力
     *
     * .htaccess を修正し対象ディレクトリをトレイリングスラッシュ無効設定から除外すること
     *
     * @param \Illuminate\Http\Request $request
     * @param null $path
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request, $path = null): BinaryFileResponse|Redirector|RedirectResponse
    {
        // 認証されていなければ拒否
        if (!Auth::check()) {
            abort(401);
        }

        // ルートに対するアクセスかつ末尾にスラッシュがついていなければリダイレクト
        // ログイン後リダイレクト時にスラッシュが削除されることに対応
        if (empty($path) && preg_match('/[^\/]$/', $_SERVER["REQUEST_URI"])) {
            return redirect($request->url() . '/');
        }

        // ファイルパス
        $file_path = resource_path('static/' . $path);

        // ファイルの存在をチェック
        if (File::exists($file_path)) {
            // ディレクトリであればindexを探す
            if (File::isDirectory($file_path)) {
                $file_path .= '/index.html';
            }
        } else {
            abort(404);
        }

        // /etc/mime.types が存在すればMIMEを取得する
        if (!Cache::has('etc_mime_types')) {
            $etc_mime_types_file = '/etc/mime.types';
            $etc_mime_types = [];
            if (File::exists($etc_mime_types_file)) {
                $lines = file($etc_mime_types_file);

                foreach ($lines as $line) {
                    // コメントを無視
                    if (str_starts_with($line, '#')) {
                        continue;
                    }

                    // MIMEを連想配列に保存
                    $mime_array = preg_split("/\s+/", $line);
                    $content_type = array_shift($mime_array);
                    if ($content_type !== '') {
                        foreach ($mime_array as $extension) {
                            if (!empty($extension)) {
                                $etc_mime_types[$extension] = $content_type;
                            }
                        }
                    }
                }
            }
            // キャッシュに保存
            Cache::put('etc_mime_types', $etc_mime_types, 60 * 60);
        }
        // キャッシュから取得
        $etc_mime_types = Cache::get('etc_mime_types');

        // 拡張子を取得
        $extension = File::extension($file_path);

        // /etc/mime.types を優先してMIMEを決定
        // File::mimeType のレスポンスが正しくないため
        $content_type = $etc_mime_types[$extension] ?? File::mimeType($file_path);

        // レスポンス
        $response = Response::file($file_path);
        $response->headers->set('Content-Type', $content_type);
        return $response;
    }
}
