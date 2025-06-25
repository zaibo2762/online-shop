<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest('created_at');

        if (!empty($request->get('keyword'))) {
            $users = User::where('name', 'LIKE', '%' . $request->get('keyword') . '%');
            $users = User::orWhere('email', 'LIKE', '%' . $request->get('keyword') . '%');
        }

        $users = $users->paginate(10);

        $data['users'] = $users;

        return view('admin.users.list', $data);
    }
    public function create(Request $request)
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',

            'phone' => 'required'
        ]);
        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'user added successfully');
            return response()->json([
                'status' => true,
                'message' => 'user added successfully'
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
        $user = User::find($id);
        if ($user == null) {
            session()->flash('error', 'user not found');
            return redirect()->route('users.index');
        }
        $data['user'] = $user;
        return view('admin.users.edit', $data);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            session()->flash('error', 'user not found');
            return response()->json([
                'status' => true,
                'message' => 'user not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'phone' => 'required'
        ]);
        if ($validator->passes()) {

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            if ($request->password != '') {
                $user->password = Hash::make($request->password);
            }


            $user->save();

            session()->flash('success', 'user added successfully');
            return response()->json([
                'status' => true,
                'message' => 'user added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            session()->flash('error', 'user not found');
            return redirect()->route('users.index');
        }

        $user->delete();
        session()->flash('success', 'user deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'User Deleted successfully'
        ]);
    }
}
