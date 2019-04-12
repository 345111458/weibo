<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller{
    //

    public function __construct(){

        $this->middleware('auth',[
            'except' => ['show','create','store','index']
        ]);

        $this->middleware('guest',[
            'only'  => ['create'],
        ]);
    }


    public function index(){

        $users = User::paginate(10);

        return view('users.index' , compact('users'));
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



    //编辑用户资料
    public function edit(User $user){
        $this->authorize('update' , $user);


        return view('users.edit',compact('user'));
    }


    // 确定修改
    public function update(User $user,Request $request){
        $this->authorize('update');

        $this->validate($request , [
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功！');
        return redirect()->route('users.show',$user);
    }


    // 删除用户
    public function destroy(User $user){
        //只有当前用户为管理员，且被删除用户不是自己时，授权才能通过
        $this->authorize('destroy' , $user);

        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();
    }





}
