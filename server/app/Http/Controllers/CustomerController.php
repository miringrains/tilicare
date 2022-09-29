<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
  // Change Avatar
  public function change_avatar(Request $request){
    $request->validate([
      'avatar' => 'required|required|max:4096',
    ]);
    // 'avatar' => 'required|required|mimes:jpeg,png,bmp,tiff|max:4096', // extension issue

    $name = 'avatar_'
      .auth()->user()->id
      ."_"
      .time()
      ."_"
      .rand(10 ** 9, 10**10-1)
      .".".$request->file('avatar')->getClientOriginalExtension();
    $request->file('avatar')->move('uploads', "$name");

    $user = auth()->user();
    $user->avatar = $name;

    if($user->save()) return back()->with([
      'success' => 'Avatar Successfully!',
    ]);
    else return back()->withErrors([
      'error' => 'Error While Changing Avatar!',
    ]);
  }
}
