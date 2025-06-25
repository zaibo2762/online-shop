<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $tempImageInfo = TempImage::find($temp_img_id);
        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();
        $imageName = $tempImageInfo->name;
        $productImage->image = $imageName;
        $productImage->save();
    }
}
