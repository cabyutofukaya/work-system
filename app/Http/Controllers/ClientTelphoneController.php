<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientTelphone;
use App\Http\Requests\UpdateClientTelphone;
use App\Models\ClientTelphone;
use Illuminate\Http\RedirectResponse;

class ClientTelphoneController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBusinessDistrict $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreClientTelphone $request): RedirectResponse
    {
        $clientsTelphone = ClientTelphone::create($request->validated());

        return redirect()->route('clients.show', ['client' => $clientsTelphone->client_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBusinessDistrict $request
     * @param \App\Models\BusinessDistrict $businessDistrict
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateClientTelphone $request, ClientTelphone $clientsTelphone): RedirectResponse
    {
        $clientsTelphone->fill($request->validated())->save();

        return redirect()->route('clients.show', ['client' => $clientsTelphone->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BusinessDistrict $businessDistrict
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ClientTelphone $clientsTelphone): RedirectResponse
    {
        $client_id = $clientsTelphone->client_id;

        $clientsTelphone->delete();

        return redirect()->route('clients.show', ['client' => $client_id]);
    }
}
