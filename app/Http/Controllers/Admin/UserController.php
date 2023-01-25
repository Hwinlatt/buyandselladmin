<?php

namespace App\Http\Controllers\Admin;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::when(request('role'),function($q){
            $q->where('role',request('role'));
        })->get();
        return view('user.index',compact('users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'password'=>'required|string|min:6',
        ]);
        $user=User::find($request->id);
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        return back()->with('message','Password change success');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('user.edit',compact('user'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$id,
            'role'=>'required',
        ]);

        $user = User::find($id);
        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'role'=>$request->role,
        ]);
        return back()->with('message','Updated Successful.');
    }

    public function destroy($id)
    {
        User::deleteUser($id);
        return response()->json(['success'=>'User Deleted'], 200);
    }

    public function role_change(Request $request)
    {
        $user = User::find($request->id);
        $user->update([
            'role'=>$request->role,
        ]);
        return response()->json(['success'=>'Role Changed'], 200);
    }
}
