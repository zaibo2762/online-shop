<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::latest('id');
        if ($request->get('keyword')) {
            $brands = $brands->where('name', 'LIKE', '%' . $request->keyword . '%');
        }
        $brands = $brands->paginate(10);


        return view('admin.brands.list', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }
    public function store(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);
        if ($Validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $Validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            session()->flash('error', 'Record Not Found');
            return redirect()->route('brands.index');
        }

        return view('admin.brands.edit', compact('brands'));
    }
    public function update($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            session()->flash('error', 'Record Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }

        $Validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brands->id . ',id',
        ]);
        if ($Validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            return response()->json([
                'status' => true,
                'message' => 'Brand Updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $Validator->errors()
            ]);
        }
    }


    public function destroy($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            session()->flash('error', 'Record Not Found');
            return response([
                'status' => false,
                'notFound' => true,

            ]);
        }
        $brands->delete();
        session()->flash('Success', 'brand Deleted Successfully');
        return response([
            'status' => true,
            'message' => "brand Deleted Successfully"
        ]);
    }
}
