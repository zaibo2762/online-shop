<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\Wishlist;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function logIn()
    {
        return view('front.account.login');
    }
    public function register()
    {
        return view('front.account.register');
    }
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);
        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'User Registed Successfully');

            return response()->json([
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                if (session('url.intended')) {
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account.profile');
            } else {
                return redirect()->route('account.login')->withInput($request->only('email'))
                    ->with('error', 'Either Email or Password is incorrect');
            }
        } else {
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }
    public function profile()
    {
        $countries =  Country::orderBy('name', 'ASC')->get();

        $address = CustomerAddress::where('user_id', Auth::user()->id)->first();

        $user = User::where('id', Auth::user()->id)->first();
        $data['user'] = $user;
        $data['address'] = $address;
        $data['countries'] = $countries;
        return view('front.account.profile', $data);
    }
    public function updateProfile(Request $request)
    {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId . 'id',
            'phone' => 'required'
        ]);
        if ($validator->passes()) {
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            session()->flash('success', 'Profile updated Successfuly');
            return response()->json([
                'status' => true,
                'message' => 'Profile updated Successfuly'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function updateAddress(Request $request)
    {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->passes()) {
            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'appartment' => $request->appartment,
                    'state' => $request->state,
                    'zip' => $request->zip,
                    'city' => $request->city,
                ]
            );
            session()->flash('success', 'User Address updated Successfuly');
            return response()->json([
                'status' => true,
                'message' => 'User Address updated Successfuly'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function logOut()
    {
        Auth::logout();
        Cart::destroy();
        return redirect()->route('account.login')->with('success', 'Successfully Logged out');
    }
    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        $data['orders'] = $orders;
        return view('front.account.order', $data);
    }
    public function orderDetail($id)
    {
        $data = [];
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->first();

        $orderItems = OrderItem::where('order_id', $id)->get();

        $data['order'] = $order;
        $data['orderItems'] = $orderItems;

        $orderItemsCount = OrderItem::where('order_id', $id)->count();
        $data['orderItemsCount'] = $orderItemsCount;
        return view('front.account.order-detail', $data);
    }
    public function wishlist()
    {
        $wishlists = Wishlist::where('user_id', Auth::user()->id)->with('product')->get();
        $data['wishlists'] = $wishlists;
        return view('front.account.wishlist', $data);
    }
    public function removeProductFromWishlist(Request $request)
    {
        $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->first();
        if ($wishlist == null) {
            session()->flash('error', 'Product already deleted');
            return response()->json([
                'status' => true
            ]);
        } else {
            Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->delete();
            session()->flash('success', 'Product removed successfully');
            return response()->json([
                'status' => true
            ]);
        }
    }
    public function showChangePasswordForm()
    {
        return view('front.account.change-password');
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);
        if ($validator->passes()) {
            $user = User::select('id', 'password')->where('id', Auth::id())->first();
            if (!Hash::check($request->old_password, $user->password)) {
                session()->flash('error', 'Your Old Password is incorrect');
                return response()->json([
                    'status' => true,
                    'message' => 'old password incorrect'
                ]);
            }
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password)

            ]);
            session()->flash('success', 'Successfully Changed Your  Password ');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function forgotPassword()
    {
        return view("front.account.forgot-password");
    }
    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()

        ]);
        $user = User::where('email', $request->email)->first();
        //Send email
        $formData = [
            'token' => $token,
            'user' => $user,
            'mailSubject' => 'You have requested to reset password'
        ];
        Mail::to($request->email)->send(new ResetPasswordEmail($formData));
        return redirect()->route('front.forgotPassword')->with('success', 'plz Check your email to reset your password');
    }
    public function resetPassword($token)
    {
        $tokenExist =  DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenExist == null) {
            return redirect()->route('front.forgotPassword')->with('error', "invalid Request");
        }
        return view('front.account.reset-password', [
            'token' => $token
        ]);
    }
    public function processResetPassword(Request $request)
    {
        $token = $request->token;
        $tokenObj =  DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenObj == null) {
            return redirect()->route('front.forgotPassword')->with('error', "invalid Request");
        }

        $user = User::where('email', $tokenObj->email)->first();
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:new_password',
        ]);
        if ($validator->fails()) {
            return redirect()->route('front.resetPassword', $token)->withErrors($validator);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        return redirect()->route('account.login')->with('success', 'You have succesfully change your password');
    }
}
