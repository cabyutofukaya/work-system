<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;

/**
 * ホーム
 * @package App\Admin\Controllers
 */
class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('ホーム')
            ->view('admin/home');
    }
}
