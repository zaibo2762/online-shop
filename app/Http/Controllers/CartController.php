<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ShippingCharge;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product = Product::with('product_image')->find($request->id);
        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found'
            ]);

        }
        if(Cart::count() > 0){
           // echo "Product already in cart";
           //Products found in cart
           //check if this product already in the cart
           //return a message 
           $cartContent = Cart::content();
           $productAlreadyExist = false;
           foreach($cartContent as $item){
            if($item->id == $product->id){
                $productAlreadyExist = true;
            }
           }
           if($productAlreadyExist == false){
            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_image)? $product->product_image->first() : '')]);
            $status = true;
            $message = $product->title.' Added in cart';


           }else{
            $status = false;
            $message = $product->title.' already Added in cart';
           }
        }else{
            
            //Cart is empty
            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_image)? $product->product_image->first() : '')]);
            $status = true;
            $message = $product->title.' Added in cart';
           
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function cart(){
        // dd(Cart::content());
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        
        return view('front.cart',$data);
    }
    public function updateCart(Request $request){

        
        $rowId = $request->rowId;
        $qty = $request->qty;
        //checking qty available in stock
        $itemInfo =  Cart::get($rowId);

        $product = Product::find($itemInfo->id);
        if($product->track_qty == 'YES'){
            if( $qty <= $product->qty){
                Cart::update($rowId,$qty);
                $message = 'Cart Updated successfully';
                $status = true;
                
            }else{
                $message = 'Requested qty('.$qty.') not available in stock';
                $status = false;
                session()->flash('error',$message);
            }

        }else{
            Cart::update($rowId,$qty);
            $message = 'Cart Updated successfully';
            $status = true;
            session()->flash('success',$message);
        }

       
        
        
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);

    }
    public function deleteItem(Request $request){
        $itemInfo =  Cart::get($request->rowId);
        if($itemInfo == null){
            $message = 'Item not found in cart';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }else{
            Cart::remove($request->rowId);
            $message = 'Item removed from cart successfully';
            session()->flash('success',$message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        }
        
        
    }
    public function checkOut(){
        if(Cart::count() == 0){
            return redirect()->route('front.cart');
        }
        if(Auth::check() == false){
            if(!session('url.intended')){
                session(['url.intended' => url()->current()]);

            }
            
            return redirect()->route('account.login');
        }
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        session()->forget('url.intended');
        $countries = Country::orderBy('name','ASC')->get();
        //calculation shipping  here
        $userCountry = $customerAddress->country_id;
        $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();
        $shippingInfo->amount;
        $totalQty = 0;
        $totalShippingCharge = 0;
        $grandTotal = 0;
        foreach(Cart::content() as $item){
            $totalQty += $item->qty;
        }
        $totalShippingCharge = $totalQty*$shippingInfo->amount;
        $grandTotal = Cart::subtotal(2,'.','')+$totalShippingCharge;
        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'grandTotal' => $grandTotal
        ]);
    }

    public function processCheckout(Request $request){
        //apply validation
        $validator = Validator::make($request->all(),[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email',
            'country'=>'required',
            'address'=>'required',
            'city'=>'required',
            'state'=>'required',
            'zip'=>'required',
            'mobile'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
               'message'=>'please fix the errors',
               'status'=> false,
               'errors'=>$validator->errors()
            ]); 
        }
        //save user address
        $user = Auth::user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' =>$user->id,
                'first_name' =>$request->first_name,
                'last_name' =>$request->last_name,
                'email' =>$request->email,
                'mobile' =>$request->mobile,
                'country_id' =>$request->country,
                'address' =>$request->address,
                'appartment' =>$request->appartment,
                'state' =>$request->state,
                'zip' =>$request->zip,
                'city' =>$request->city,
            ]
        );
        //store data in order table
      
        if($request->payment_method == 'cod'){

            //calculate Shipping
            $shiping = 0;
            $discount = 0;
            $subtotal = Cart::subtotal(2,'.','');
           
            $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();
            $totalQty = 0;
        
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
            if($shippingInfo != null){
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = $subtotal + $shipping;
                
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = $subtotal + $shipping;
              
            }
            
            $order = new Order;
            $order->subtotal = $subtotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->appartment = $request->appartment;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->country_id = $request->country;
            $order->save();
        }else{
            //
        }
        //store items in the order item table
        foreach (Cart::content() as $item) {
            $orderitem = new OrderItem;
            $orderitem->product_id = $item->id;
            $orderitem->order_id = $order->id;
            $orderitem->name = $item->name;
            $orderitem->qty = $item->qty;
            $orderitem->price = $item->price;
            $orderitem->total = $item->price*$item->qty;
            $orderitem->save();
        }
        Cart::destroy();
        session()->flash('success','you have successfully placed your order');
        return response()->json([
            'message'=>'order saved successfully',
            'orderId'=> $order->id,
            'status'=> true,
         ]); 

    }
    public function thankyou($id){
        session()->flash('success','Your order is successfully placed');
        return view('front.thanks',[
            'id' => $id
        ]);
    }
    public function getOrderSummary(Request $request){
        $subTotal = Cart::subtotal(2,'.','');
        if($request->country_id > 0){
        $totalQty = 0;
        
        foreach(Cart::content() as $item){
            $totalQty += $item->qty;
        }
            
            $shippingInfo = ShippingCharge::where('country_id',$request->country_id)->first();
            if($shippingInfo != null){
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandtotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2)

                ]);
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal + $shippingCharge;
                
                return response()->json([
                    'status' => true,
                    'grandtotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2)

                ]);
            }
        }else{
             
            return response()->json([
                'status' => true,
                'grandtotal' => number_format($subTotal,2),
                    'shippingCharge' => number_format(0,2)
            ]);
        }
    }
}
