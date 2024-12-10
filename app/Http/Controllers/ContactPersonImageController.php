<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactPersonImage;
use App\Models\Base\Client;
use App\Models\ContactPerson;
use App\Models\ContactPersonImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Storage;
use Log;

class ContactPersonImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreContactPerson $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreContactPersonImage $request): RedirectResponse
    {
        $contact_person = ContactPerson::find($request->id);

        ContactPersonImage::create([
            'name' => $request->name,
            'contact_person_id' => $request->id,
        ]);

        return redirect()->route('clients.show', ['client' => $contact_person->client_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ContactPerson $contactPerson
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destory(ContactPersonImage $contactPersonImage): RedirectResponse
    {


        ContactPersonImage::where('id',$contactPersonImage->id)->update([
            'deleted_at' => date('Y-m-d G:i:s'),
        ]);
     

        return redirect()->back();
    }


    public function upload(Request $request)
    {
        Log::debug('uu');
        $image = $request->file('image');

       
        $extension = $image->getClientOriginalExtension();

        $fileName = sha1(uniqid(mt_rand(), true)) . date('YmdGis') . "." . $extension;

        $tmpPath = storage_path('app/tmp/') . $fileName;

        //保存(URL取得)
        $path = Storage::putFileAs('public/client/person/', $image, $fileName);

        // 一時ファイルを削除
        \Storage::disk('local')->delete('tmp/' . $fileName);

        // \Storage::disk('s3')->putFile('/content', $request->image, 'public');

        return response()->json([
            'name' => $fileName,
        ], 200);
    }
}
