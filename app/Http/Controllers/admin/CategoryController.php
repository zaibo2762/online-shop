<?php

namespace App\Http\Controllers\admin;


use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }
    public function create()
    {
        return view('admin.category.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            //Save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray =  explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImage = $category->id . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/category/' . $newImage;
                File::copy($spath, $dpath);
                $category->image = $newImage;
                $category->save();
            }

            session()->flash('success', 'Category Added Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }
    }
    public function edit($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit', compact('category'));
    }
    public function update($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                'status' => false,
                'notfound' => true,
                'message' => 'Category Not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id',
        ]);
        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();



            //Save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray =  explode('.', $tempImage->name);
                $ext = last($extArray);
                $oldImage = $category->id . '-' . time() . '.' . $ext;
                $newImage = $category->id . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/category/' . $newImage;
                File::copy($spath, $dpath);
                $category->image = $newImage;
                $category->save();

                //Delete old Images
                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }

            session()->flash('success', 'Category Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }
    }
    public function destroy($categoryId, Request $request)
    {
        $category = Category::find($categoryId);

        if (empty($category)) {
            // return redirect()->route('categories.index');
            session()->flash('error', 'Category not found');
            return response()->json([

                'status' => false,
                'message' => 'Category  Not found'
            ]);
        }
        File::delete(public_path() . '/uploads/category/' . $category->image);
        $category->delete();
        session()->flash('Category Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}
