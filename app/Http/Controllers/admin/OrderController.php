<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.user_id');
        if($request->get('keyword') != ''){
           $orders = $orders->where('users.name', 'like','%'.$request->keyword.'%');
           $orders = $orders->orWhere('users.email', 'like','%'.$request->keyword.'%');
           $orders = $orders->orWhere('orders.id', 'like','%'.$request->keyword.'%');
        }
        $orders = $orders->paginate(10);
        $data['orders'] = $orders;
        return view('admin.orders.list',$data);
    }
    public function detail($id){
        $order = Order::select('orders.*','countries.name as countryName')
                ->where('orders.id',$id)
                ->leftJoin('countries','countries.id','=','orders.country_id')
                ->first();

        $orderItems = OrderItem::where('order_id',$id)->get();
        $data['order'] = $order;
        $data['orderItems'] = $orderItems;
        return view('admin.orders.detail',$data);
    }
    public function changeOrderStatus(Request $request,$id){
        $order = Order::find($id);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();
        session()->flash('status','Order Status Changed successfully');
        return response()->json([
            'status' => true,
            'message' => 'Status Changed succesfully'
        ]);


    }
    public function sendInvoiceEmail(Request $request,$id){

        orderEmail($id, $request->userType);
         session()->flash('status','Order email send successfully');
        return response()->json([
            'status' => true,
            'message' => 'order email send succesfully'
        ]);

    }
}
