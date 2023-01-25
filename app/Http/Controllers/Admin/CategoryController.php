<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return view('category.index',compact('categories'));
    }


    public function create()
    {
        return view('category.create');
    }


    public function store(Request $request)
    {
        $request->validate($this->validation(NULL));
        Category::create([
            'name'=>$request->name,
            'icon'=>$request->icon,
        ]);
        return redirect()->route('category.index')->with('message','Category Created.');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $category = Category::find($id);
        return view('category.edit',compact('category'));
    }


    public function update(Request $request, $id)
    {
        $request->validate($this->validation($id));
        $category = Category::find($id);
        $category->update([
            'name'=>$request->name,
            'icon'=>$request->icon
        ]);
        return back()->with('message','Updated success.');
    }


    public function destroy($id)
    {
        Category::find($id)->delete();
        return response()->json(['success'=>'Category Deleted'], 200);
    }

    private function validation($id){
       $return =  [
            'name'=>'required|unique:categories,name,'.$id,
            'icon'=>'required|unique:categories,icon,'.$id,
        ];
        return $return;
    }
}
