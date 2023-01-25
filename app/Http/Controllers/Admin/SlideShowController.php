<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlideShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SlideShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slides = SlideShow::all();
        return view('slideshow.index', compact('slides'));
    }

    public function create()
    {
        return view('slideshow.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->validation('create'));
        $file_name = time() . '-' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $slide = SlideShow::create([
            'title' => $request->title,
            'image' => $file_name,
            'link' => $request->link,
        ]);
        $request->file('image')->storeAs('public/images', $slide->image);
        return redirect()->route('slideshow.index')->with('message', 'Slideshow created.');
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $slide = SlideShow::find($id);
        return view('slideshow.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->validation('update'));
        $slide = SlideShow::find($id);
        $slide->update([
            'title' => $request->title,
            'link' => $request->link,
        ]);
        if ($request->hasFile('image')) {
            $path = 'storage/images/'.$slide->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file_name = time() . '-' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $slide->update([
                'image'=>$file_name
            ]);
            $request->file('image')->storeAs('public/images', $slide->image);
        }
        return back()->with('message','Updated success');
    }
    public function destroy($id)
    {
        $slide = SlideShow::find($id)->delete();
        return response()->json(['success'=>'Slideshow Deleted'], 200);
    }

    private function validation($type)
    {
        $return = [
            'title' => 'required|string|max:200',
            'image' => 'image',
            'link' => 'required',
        ];
        if ($type == 'create') {
            $request['image'] = 'required|image';
        }
        return $return;
    }
}
