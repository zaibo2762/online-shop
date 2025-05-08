<?php

namespace App\Http\Controllers\admin;

use App\Models\Country;

use Illuminate\Http\Request;
use App\Models\ShippingCharge;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $countries = Country::get();
        $shippingcharges = ShippingCharge::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['countries'] = $countries;
        $data['shippingcharges'] = $shippingcharges;
        return view('admin.shipping.create',$data);
    }
    public function store(Request $request){
       
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);
        if($validator->passes()){
            $count = ShippingCharge::where('country_id',$request->country)->count();
            if($count > 0){
                session()->flash('error','shipping Already Added');
                return response()->json([
                    'status' => true
                ]);
            }
    
            $shipping =  new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save(); 
            session()->flash('success','shipping Added successfully');
            return response()->json([
                'status' => true
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id){
        $shippingcharge = ShippingCharge::find($id);
        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingcharge'] = $shippingcharge;
        return view('admin.shipping.edit',$data);
    }
    public function update($id,Request $request){
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);
        if($validator->passes()){
            $shipping =   ShippingCharge::find($id);
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save(); 
            session()->flash('success','shipping updated successfully');
            return response()->json([
                'status' => true
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy($id){
      $shippingcharge = ShippingCharge::find($id);
      if($shippingcharge == null){
        session()->flash('success','shipping record not found');
        return response()->json([
            'status' => true
        ]);
      }
      $shippingcharge->delete();

      session()->flash('success','shipping deleted successfully');
      return response()->json([
          'status' => true
      ]);
    }
}
