<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\TempImage;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::latest('id')->with('product_image');
        if($request->get('keyword')!= '' ){
            $products = $products->where('title','LIKE','%'.$request->keyword.'%');
        }
        $products = $products->paginate();
        // dd($products);
        return view('admin.products.list',compact('products'));
    }
    public function create(){
        
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create',$data);
    }
    public function store(Request $request){
        
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:YES,NO'
        ];
        if (!empty($request->track_qty) && $request->track_qty == 'YES') {
            $rules['qty'] = 'required|numeric'; 
        }

        $validator = Validator::make($request->all(),$rules);
        if($validator->passes()){
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products))? implode(',',$request->related_products) : '';
            $product->save();

            //Save Gallery Pictures

            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_img_id) {

                    $tempImageInfo = TempImage::find($temp_img_id);
                    $extArray = explode('.',$tempImageInfo->name) ;
                    $ext = last($extArray);
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage -> image = 'NULL';
                    $productImage->save();
                    $imageName = $tempImageInfo->name;
                    $productImage ->image = $imageName;
                    $productImage->save();
                }
            }

            session()->flash('success','Product Added successfullly');

            return response()->json([
                'status'=>true,
                'message'=>'Product Added successfullly'
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }


    public function edit($id,Request $request){

        $product = Product::find($id);

        if(empty($product)){
            return redirect()->route('products.index')->with('error','PRoduct Not found');
        }
        //Fetch Product Image
        $productImages = ProductImage::where('product_id',$product->id)->get();

        $subCategories = SubCategory::where('category_id',$product->category_id)->get();

        //Fetch Related Products
        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products );
            $relatedProducts = Product::whereIn('id',$productArray)->get();
        }
         
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['product'] = $product;
        $data['productImages'] = $productImages;
        $data['subCategories'] = $subCategories;
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.products.edit',$data);
    }

    public function update($id, Request $request){

        $product = Product::find($id);

       

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:YES,NO'
        ];
        if (!empty($request->track_qty) && $request->track_qty == 'YES') {
            $rules['qty'] = 'required|numeric'; 
        }



        $validator = Validator::make($request->all(),$rules);
        if($validator->passes()){
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products))? implode(',',$request->related_products) : '';
            $product->save();

            //Save Gallery Pictures


            session()->flash('success','Product Updated successfullly');

            return response()->json([
                'status'=>true,
                'message'=>'Product updated successfullly'
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($id,Request $request){
        $product = Product::find($id);
        if(empty($product)){
         session()->flash('error','Product Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true 
            ]);
        }
        $productImages = ProductImage::where('product_id',$id)->get();
        if(!empty($productImages)){
        foreach($productImages as $productImage ){
          File::delete(public_path('temp/'.$productImage->image));
        }
        }
     ProductImage::where('product_id',$id)->delete();
     $product->delete();
     session()->flash('success','Product Deleted Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product Deleted Successfully' 
            ]);
        
    }
    public function getProducts(Request $request){
        $tempProducts = [];
        if($request->term != ""){
            $products = Product::where('title',"like",'%'.$request->term.'%')->get();
            if($products != null){
                foreach ($products as  $product) {
                    $tempProducts[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }
        return response()->json([
            'tags' => $tempProducts,
            'status' => true
        ]);
    }
}
