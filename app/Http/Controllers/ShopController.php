<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {

        $categorySelected = '';
        $subcategorySelected = '';
        $brandsArray = [];


        $category = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
        $products = Product::where('status', 1);

        //Applying filters here
        if (!empty($categorySlug)) {
            $catgory = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $catgory->id);
            $categorySelected = $catgory->id;
        }
        if (!empty($subCategorySlug)) {
            $subcategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subcategory->id);
            $subcategorySelected = $subcategory->id;
        }


        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        if (!empty($request->get('search'))) {
            $products = $products->where('title', 'LIKE', '%' . $request->get('search') . "%");
        }

        $products->orderBy('id', 'DESC');
        $products = $products->paginate(6);


        $data['category'] = $category;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['subcategorySelected'] = $subcategorySelected;
        return view('front.shop', $data);
    }
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['product_image', 'product_ratings'])->first();
        if ($product == Null) {
            abort(404);
        }

        //fetch related product
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->where('status', 1)->with('product_image')->get();
        }

        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        return view("front.product", $data);
    }
    public function saveRating(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:10',
            'email' => 'required|email',
            'comment' => 'required',
            'rating' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $count = ProductRating::where('email', $request->email)->count();
        if ($count > 0) {
            session()->flash('error', 'you already given the review');
            return response()->json([
                'status' => true
            ]);
        }

        $productRating = new ProductRating;
        $productRating->product_id = $id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();
        session()->flash('success', 'Thanks for your review');
        return response()->json([
            'status' => true,
            'message' => 'Thanks for your review'
        ]);
    }
}
