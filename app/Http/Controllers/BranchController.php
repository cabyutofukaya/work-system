<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranch;
use App\Http\Requests\UpdateBranch;
use App\Models\Branch;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\ResponseFactory;

class BranchController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function create(Client $client): Response|\Inertia\Response|ResponseFactory
    {
        return inertia('BranchesCreate', [
            'client' => $client,
            // 都道府県
            'prefectures' => config("const.prefectures"),
            // 位置情報初期値
            'location_default' => config("const.location_default"),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Models\Client $client
     * @param \App\Http\Requests\StoreBranch $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Client $client, StoreBranch $request): RedirectResponse
    {
        $branch = new Branch;
        $branch->fill($request->validated());
        $branch->client_id = $client->id;
        $branch->save();

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $client->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Branch $branch
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(Branch $branch): \Inertia\Response|ResponseFactory
    {
        // グローバルメニューのアクティブ判定のため会社タイプを取得する
        $branch->load(["client:id,client_type_id"]);

        return inertia('BranchesEdit', [
            'branch' => $branch,
            // 都道府県
            'prefectures' => config("const.prefectures"),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBranch $request
     * @param \App\Models\Branch $branch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBranch $request, Branch $branch): RedirectResponse
    {
        $branch->fill($request->validated())->save();

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $branch->client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $branch->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Branch $branch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        $client_id = $branch->client_id;

        $branch->delete();

        // バックボタンの戻り先ページを設定
        request()->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $branch->client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $client_id]);
    }
}
