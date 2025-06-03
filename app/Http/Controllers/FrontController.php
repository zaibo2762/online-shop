<?php

namespace App\Http\Controllers;


use App\Models\Page;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index(){
        $products = Product::where('is_featured','Yes')->orderBy('id','DESC')->where('status',1)->take(8)->get();
        $latestproducts = Product::orderBy('id','ASC')->where('status',1)->take(8)->get();
        $data['featuredProducts'] = $products; 
        $data['latestproducts'] = $latestproducts; 
        return view('front.home',$data);
    }
    public function addToWishlist(Request $request){
        if(Auth::check() == false){

            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status'=>false,
                
            ]);
        }

        $product = Product::where('id',$request->id)->first();

        if($product == null){
             return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-danger"> Product not found </div>'
        ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ],
            [
                 'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ] 
            );

        // $wishlist = new Wishlist;
        // $wishlist->user_id = Auth::User()->id;
        // $wishlist->product_id = $request->id;
        // $wishlist->save();
        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success">'.$product->title.' Added In wishlist </div>'
        ]);

    }
    public function page($slug){
        $page = Page::where('slug',$slug)->first();
        if($page == null){
            abort(404);
        }
        $data['page'] = $page;
        return view('front.page',$data);
    }
    public function sendContactEmail(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'subject'  => 'required'
        ]);
        if($validator->passes()){
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have recieved a contact email.'

            ];
            $admin = User::where('role',2)->first();

            Mail::to($admin->email)->send(new ContactEmail($mailData));
                session()->flash('success','Thanks For Conatcting Us We Will Get Back To You Soon');
             return response()->json([
                'status'=> true
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }
    }


}
