<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller{
    //SessionController
    public function __construct(){

        $this->middleware('guest',[
            'only'  => ['create'],
        ]);
    }


    // 登陆页面显示
    public function create(){


        return view('sessions.create');
    }


    // 登陆数据验证
    public function store(Request $request){

        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)){
            session()->flash('success','欢迎回来！');

            $fallback = route('users.show',[Auth::user()]);
            return redirect()->intended($fallback);
        }else{
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();

        }

        return ;
    }


    public function destroy(){
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }




}
