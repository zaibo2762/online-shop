<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $discountCoupons = DiscountCoupon::latest();
        if (!empty($request->get('keyword'))) {
            $discountCoupons = $discountCoupons->where('name', 'like', '%' . $request->get('keyword') . '%');
            $discountCoupons = $discountCoupons->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
        }
        $discountCoupons = $discountCoupons->paginate(10);
        return view('admin.coupon.list', compact('discountCoupons'));
    }
    public function create()
    {
        return view('admin.coupon.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'starts_at'       => 'nullable|date|after:now',
            'expires_at'      => 'nullable|date|after:starts_at',
        ]);
        if ($validator->passes()) {
            //starting date is must be greater than current date
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if ($startsAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start Date can not be less than current time']
                    ]);
                }
            }

            //expiry date must be greater than start date

            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if ($expiresAt->lte($startsAt) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'expiry Date can not be less than start date']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon();
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_users;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();
            session()->flash('success', 'Discount Coupon Added Successfully');
            return response()->json([
                'status' => true,
                'message' => 'discount coupon added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit(Request $request, $id)
    {

        $coupon = DiscountCoupon::find($id);
        if ($coupon == null) {
            session()->flash('error', 'Record Not Found');
            return redirect()->route('coupons.index');
        }

        $data['coupon'] = $coupon;
        return view('admin.coupon.edit', $data);
    }
    public function update(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            session()->flash('error', 'Record not found');
            return response()->json([
                'status' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'starts_at'       => 'nullable|date|after:now',
            'expires_at'      => 'nullable|date|after:starts_at',
        ]);
        if ($validator->passes()) {


            //expiry date must be greater than start date

            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if ($expiresAt->lte($startsAt) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'expiry Date can not be less than start date']
                    ]);
                }
            }


            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_users;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();
            session()->flash('success', 'Discount Coupon updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'discount coupon updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy(Request $rquest, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            session()->flash('error', 'Record not found');
            return response()->json([
                'status' => true
            ]);
        }

        $discountCode->delete();
        session()->flash('success', 'Discount Coupon Deleted Successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
