<?php

use App\Models\Page;
use App\Models\Order;
use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

    function getCategories(){
        return Category::orderBy('name','ASC')->with('sub_category')->orderBy('id','DESC')->where('status',1)->where('showHome','Yes')->get();
    }
    function getProductImage($productId){
        return ProductImage::where('product_id',$productId)->first();
    }
    function orderEmail($orderId,$userType = 'customer'){
        $order = Order::where('id',$orderId,)->with('Items')->first();
        if($userType == 'customer'){
            $subject =   'Thanks For your order';
            $email = $order->email;          
        }else{
             $subject =   'You have recieved an order';
             $email = env("ADMIN_EMAIL");
        }
        $mailData = [
            'subject' => $subject,
            'order' => $order,
            'userType' => $userType
        ];
        Mail::to($email)->send(new OrderEmail($mailData));  
    }
    function staticPages(){
      $page = Page::orderBy('name','ASC')->get();
      return $page;
    }
?>