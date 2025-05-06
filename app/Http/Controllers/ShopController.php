<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request,$categorySlug = null ,$subCategorySlug = null ){

        $categorySelected = '';
        $subcategorySelected = '';
        $brandsArray = [];

       
        $category = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
        $brands = Brand::orderBy('name','ASC')->where('status',1)->get();
        $products = Product::where('status',1);

        //Applying filters here
        if(!empty($categorySlug)){
            $catgory = Category::where('slug',$categorySlug)->first();
            $products = $products->where('category_id',$catgory->id);
            $categorySelected = $catgory->id;
        }
        if(!empty($subCategorySlug)){
            $subcategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products = $products->where('sub_category_id',$subcategory->id);
            $subcategorySelected = $subcategory->id;
        }


        if(!empty($request->get('brand'))){
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->whereIn('brand_id',$brandsArray);
        }

        $products->orderBy('id', 'DESC');
        $products = $products->paginate(6);
        

        $data['category'] = $category;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['subcategorySelected'] = $subcategorySelected;
        return view('front.shop',$data);
    }
    public function product($slug){
        $product = Product::where('slug',$slug)->with('product_image')->first();
       if($product == Null){
        abort(404);
       }
       $data['product'] = $product;
       return view("front.product",$data);

    }
}
