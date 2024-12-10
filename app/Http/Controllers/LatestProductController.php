<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowClient;
use App\Http\Requests\StoreClient;
use App\Http\Requests\UpdateClient;
use App\Models\Client;
use App\Models\ClientTypeRestaurant;
use App\Models\ClientTypeTaxibus;
use App\Models\ClientTypeTravel;
use App\Models\ClientTypeTruck;
use App\Models\Genre;
use App\Models\Evaluation;
use App\Models\LatestEvaluation;
use App\Models\Report;
use App\Models\ReportContentProduct;
use App\Models\Product;
use App\Models\ReportContent;
use App\Models\SalesTodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Inertia\ResponseFactory;
use Lang;

class LatestProductController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreClient $request
     * @param $client_type_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function select(int $latest_product_id)
    {
        $latest_product = LatestEvaluation::where('id', $latest_product_id)->first();

        return json_encode($latest_product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreClient $request
     * @param $client_type_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {

        $report = Report::create([
            'date' => date('Y-m-d'),
            'draft_flg' => 1,
            'user_id' => Auth::user()->id,
        ]);

        $report_content = ReportContent::create([
            'report_id' => $report->id,
            'type' => 'sales',
            'client_id' => $request->client_id,
            'hidden' => 1,
            'product_bikou' => $request->bikou,
        ]);

        ReportContentProduct::create([
            'report_content_id' => $report_content->id,
            'product_id' => $request->product_id,
            'evaluation_id' => $request->evaluation_id,
            'product_description' => $request->bikou,
        ]);


        LatestEvaluation::where([
            'product_id' => $request->product_id,
            'client_id' => $request->client_id,
        ])->delete();

        LatestEvaluation::create([
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'evaluation_id' => $request->evaluation_id,
            'report_content_id' => $report_content->id,
            'bikou' => $request->bikou,
        ]);

        return redirect()->route('clients.show', [
            'client' => $request->client_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateClient $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LatestEvaluation $latest_product, Request $request)
    {
        LatestEvaluation::where('id', $latest_product->id)->update([
            'evaluation_id' => $request->evaluation_id,
            'user_id' => Auth::user()->id,
            'bikou' => $request->bikou,
        ]);

        $report_content_product = ReportContentProduct::where([
            'product_id' => $latest_product->product_id,
            'evaluation_id' => $latest_product->evaluation_id,
            'user_id' => $latest_product->user_id,
        ])->orderBy('updated_at', 'DESC')->first();

        ReportContentProduct::where([
            'id' => $report_content_product->id,
        ])->update([
            'evaluation_id' => $request->evaluation_id,
            'user_id' => Auth::user()->id,
            'product_description' => $request->bikou,
        ]);

        $report_content = ReportContent::where([
            'id' => $report_content_product->report_content_id,
        ])->first();

        ReportContent::where('id', $report_content->id)->update([
            'updated_at' => now()
        ]);

        $report = Report::where([
            'id' => $report_content->report_id,
        ])->first();


        $report = Report::where([
            'id' => $report->id,
        ])->update([
            'updated_at' => now()
        ]);




        return redirect()->route('clients.show', ['client' => $request->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client_type_id = $client->client_type_id;

        $client->delete();

        // バックボタンの戻り先ページを設定
        request()->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('client-types.clients.index', ['client_type' => $client_type_id]);
    }
}
