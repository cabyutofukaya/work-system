<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactPerson;
use App\Http\Requests\UpdateContactPerson;
use App\Models\ContactPerson;
use Illuminate\Http\RedirectResponse;

class ContactPersonController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreContactPerson $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreContactPerson $request): RedirectResponse
    {
        $contactPerson = ContactPerson::create($request->validated());

        return redirect()->route('clients.show', ['client' => $contactPerson->client_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateContactPerson $request
     * @param \App\Models\ContactPerson $contactPerson
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateContactPerson $request, ContactPerson $contactPerson): RedirectResponse
    {
        $contactPerson->fill($request->validated())->save();

        return redirect()->route('clients.show', ['client' => $contactPerson->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ContactPerson $contactPerson
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ContactPerson $contactPerson): RedirectResponse
    {
        $client_id = $contactPerson->client_id;

        $contactPerson->delete();

        return redirect()->route('clients.show', ['client' => $client_id]);
    }
}
