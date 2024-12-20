<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use Intervention\Image\Facades\Image;

class UploadImageController extends Controller
{

    function upload(Request $request)
    {
        Log::debug('upload_file');


        $file = $request->file;

        Log::debug($file);
        Log::debug(exif_imagetype($request->file));


        $extension = $file->getClientOriginalExtension();

        $original_name = $file->getClientOriginalName();

        if ($extension == 'pdf' || $extension == 'csv' || $extension == 'txt' || $extension == 'xlsx' || $extension == 'xlsm') {

            $fileName = sha1(uniqid(mt_rand(), true)) . date('YmdGis') . "." . $extension;

            Storage::putFileAs('public/report/', $file, $fileName);

        } else {

            $fileName = sha1(uniqid(mt_rand(), true)) . date('YmdGis') . ".webp";

            
            // $fileName = sha1(uniqid(mt_rand(), true)) . date('YmdGis') . ".webp";

            $resized_image = Image::make($request->file)->resize(900, 900, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // 小さい画像は大きくしない
            })->orientate()->save();

            Storage::put('public/report/' . $fileName, $resized_image, 90);

        }

        Log::debug('333');

        return response()->json([
            'name' => $fileName,
            'original_name' => $original_name
        ], 200);
    }
}
