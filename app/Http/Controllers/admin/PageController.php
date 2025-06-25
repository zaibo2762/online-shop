<?php

namespace App\Http\Controllers\admin;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::latest();

        if ($request->keyword != '') {
            $pages = Page::where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        $pages = $pages->paginate(10);

        $data['pages'] = $pages;

        return view('admin.pages.list', $data);
    }
    public function create()
    {
        return view('admin.pages.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();
        session()->flash('success', 'Page Added Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Page Added Successfully'
        ]);
    }
    public function edit($id)
    {
        $page = Page::find($id);
        if ($page == null) {
            session()->flash('error', 'Page Not Found');
            return redirect()->route('pages.index');
        }
        $data['page'] = $page;
        return view('admin.pages.edit', $data);
    }
    public function update(Request $request, $id)
    {
        $page = Page::find($id);

        if ($page == null) {
            if ($page == null) {
                session()->flash('error', 'Page Not Found');
                return redirect()->route('pages.index');
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();
        session()->flash('success', 'Page updated Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Page updated Successfully'
        ]);
    }
    public function destroy($id)
    {
        $page = Page::find($id);
        if ($page == null) {
            session()->flash('error', 'Page Not Found');
            return redirect()->route('pages.index');
        }
        $page->delete();
        session()->flash('success', 'Page Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Page deleted Successfully'
        ]);
    }
}
