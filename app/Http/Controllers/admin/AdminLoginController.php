<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(
                ['email' => $request->email, 'password' => $request->password],
                $request->filled('remember')
            )) {
                $admin = Auth::guard('admin')->user();
                if ($admin->role == 2) {
                    return redirect()->route('admin.dashboard');
                } else {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not Authorised to access admin panel.');
                }
            } else {
                return redirect()->route('admin.login')->with('error', 'Either Email/password is incorrected');
            }
        } else {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }
}
