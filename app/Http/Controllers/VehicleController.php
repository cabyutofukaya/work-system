<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicle;
use App\Http\Requests\UpdateVehicle;
use App\Models\Client;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;

class VehicleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\Client $client
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function create(Client $client): Response|ResponseFactory
    {
        return inertia('VehiclesCreate', [
            'client' => $client,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Models\Client $client
     * @param \App\Http\Requests\StoreVehicle $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Client $client, StoreVehicle $request): RedirectResponse
    {
        $vehicle = new Vehicle();
        $vehicle->fill($request->validated());
        $vehicle->client_id = $client->id;

        // 画像ファイルをストレージに保存
        if ($request->hasFile('_image')) {
            $file = $request->file('_image');
            $vehicle->image = $file->store('vehicles');
        }

        $vehicle->save();

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $client->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Vehicle $vehicle): Response|ResponseFactory
    {
        return inertia('VehiclesShow', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(Vehicle $vehicle): Response|ResponseFactory
    {
        return inertia('VehiclesEdit', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateVehicle $request
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateVehicle $request, Vehicle $vehicle): RedirectResponse
    {
        // 車両情報
        $vehicle->fill($request->validated());

        if ($request->hasFile('_image')) {
            // 画像ファイルを取得
            $file = $request->file('_image');

            // ファイルをストレージに保存
            $vehicle->image = $file->store('clients');
        }

        $vehicle->save();

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('clients.show', ['client' => $vehicle->client_id]),
            "hash" => "vehicles-" . $vehicle->type,
        ]);

        return redirect()->route('vehicles.show', ['vehicle' => $vehicle->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $client = $vehicle->client;

        $vehicle->delete();

        // バックボタンの戻り先ページを設定
        request()->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $client->id]);
    }
}
