<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $products = Product::where('is_featured','Yes')->orderBy('id','DESC')->where('status',1)->take(8)->get();
        $latestproducts = Product::orderBy('id','ASC')->where('status',1)->take(8)->get();
        $data['featuredProducts'] = $products; 
        $data['latestproducts'] = $latestproducts; 
        return view('front.home',$data);
    }
}
