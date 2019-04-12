<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
    }


}
