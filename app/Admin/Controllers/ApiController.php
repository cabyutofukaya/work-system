<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * 営業所セレクタ カスケード選択API
     *
     * @queryParam q required 会社ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function branches(): JsonResponse
    {
        $branches = Branch::where("client_id", request()->input("q"))->get(["id", "name"]);

        return response()->json($branches);
    }
}
