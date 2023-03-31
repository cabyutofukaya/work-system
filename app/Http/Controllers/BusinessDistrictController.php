<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessDistrict;
use App\Http\Requests\UpdateBusinessDistrict;
use App\Models\BusinessDistrict;
use Illuminate\Http\RedirectResponse;

class BusinessDistrictController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBusinessDistrict $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBusinessDistrict $request): RedirectResponse
    {
        $businessDistrict = BusinessDistrict::create($request->validated());

        return redirect()->route('clients.show', ['client' => $businessDistrict->client_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBusinessDistrict $request
     * @param \App\Models\BusinessDistrict $businessDistrict
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBusinessDistrict $request, BusinessDistrict $businessDistrict): RedirectResponse
    {
        $businessDistrict->fill($request->validated())->save();

        return redirect()->route('clients.show', ['client' => $businessDistrict->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BusinessDistrict $businessDistrict
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BusinessDistrict $businessDistrict): RedirectResponse
    {
        $client_id = $businessDistrict->client_id;

        $businessDistrict->delete();

        return redirect()->route('clients.show', ['client' => $client_id]);
    }
}
