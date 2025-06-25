<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = rand() . '.' . $ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path() . '/temp', $newName);
            //Generate thumbnail
            $sourcePath = public_path() . '/temp/' . $newName;




            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'Image_url' => $sourcePath,
                'message' => 'Image Updated Successfully'

            ]);
        }
    }
}
