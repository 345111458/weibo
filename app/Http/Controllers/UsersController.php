<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller{
    //

    public function index(){

        echo 'aaasss';
    }
    public function create(){


        return view('users.create');
    }


    public function show(User $user){

        return view('users.show', compact('user'));
    }


    //用于处理用户创建的相关逻辑。
    public function store(Request $request){

        $this->validate($request , [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);


        // 注册成功 保存用户并重定向
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
    }





}
