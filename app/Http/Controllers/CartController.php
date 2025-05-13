<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
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
      
        $discount = 0;
       



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
        $subTotal =  Cart::subtotal(2,'.','');
         //Apply Discount Here
         if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }

        }

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
        $grandTotal =( $subTotal-$discount)+$totalShippingCharge;
        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'discount' =>$discount,
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
            $discountCodeId = NULL;
            $promoCode = '';
            $subtotal = Cart::subtotal(2,'.','');
            $discount = 0;
            //Apply Discount Here
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                    $discount = ($code->discount_amount/100)*$subtotal;
                    $discountCodeId = $code->id;
                    $promoCode = $code->code;
                }else{
                    $discount = $code->discount_amount;
                    $discountCodeId = $code->id;
                    $promoCode = $code->code;
                }
              
    
            }
           
            $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();
            $totalQty = 0;
        
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
            if($shippingInfo != null){
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subtotal-$discount) + $shipping;
                
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subtotal-$discount) + $shipping;
              
            }
            
          

            $order = new Order;
            $order->subtotal = $subtotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
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
        //send order email
        orderEmail($order->id);
        
        Cart::destroy();
        session()->forget('code');
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
        $discount = 0;
        $discountString = '';
        //Apply Discount Here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }
            $discountString =  '<div class="mt-4" id="discount-response">
            <strong>'.session()->get('code')->code.'</strong>
            <a class="btn btn-danger btn-sm" id="remove_discount"><i class="fa fa-times"></i></a>
         </div> ';

        }
    

        if($request->country_id > 0){
        $totalQty = 0;
        
        foreach(Cart::content() as $item){
            $totalQty += $item->qty;
        }
            
            $shippingInfo = ShippingCharge::where('country_id',$request->country_id)->first();
            if($shippingInfo != null){
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandtotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)

                ]);
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount) + $shippingCharge;
                
                return response()->json([
                    'status' => true,
                    'grandtotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)

                ]);
            }
        }else{
             
            return response()->json([
                'status' => true,
                'grandtotal' => number_format(($subTotal-$discount),2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2)
            ]);
        }
    }
    public function applyDiscount(Request $request){
        $code = DiscountCoupon::where('code',$request->code)->first();
        if($code == null){
            return response()->json([
                'status' => false,
                'message' =>  'invalid discount coupon code'
            ]);
        }
        //check if coupon start date is valid or not
        $now = Carbon::now();
        if($code->starts_at != ''){
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at);
            if($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' =>  'invalid discount coupon not start'
                ]);
            }
        }
        if($code->expires_at != ''){
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->expires_at);
            if($now->gt($endDate)){
                return response()->json([
                    'status' => false,
                    'message' =>  'invalid discount coupon expired'
                ]);
            }
        }
        if($code->max_uses > 0){
        $couponUsed = Order::where('coupon_code_id',$code->id)->count();

        if($couponUsed >= $code->max_uses){
             return response()->json([
                    'status' => false,
                    'message' =>  'invalid discount coupon expired'
                ]);
        }
        }
        if($code->max_uses_user > 0){
                 $couponUsedUser = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
       if($couponUsedUser >= $code->max_uses_user){
             return response()->json([
                    'status' => false,
                    'message' =>  'You already USed this Coupon'
                ]);
        }
        }
         $subTotal =  Cart::subtotal(2,'.','');
         if($code->min_amount > 0 ){
            if($subTotal < $code->min_amount){
                 return response()->json([
                    'status' => false,
                    'message' =>  'Your Min amount must be $'.$code->min_amount.'.',
                ]);
            }

         }


       

        session()->put('code',$code);
        return $this->getOrderSummary($request);   
    }
    public function removeCoupon(Request $request){
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
