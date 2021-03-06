<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;


class UsersController extends Controller{
    //

    public function __construct(){

        $this->middleware('auth',[
            'except' => ['show','create','store','index','confirmEmail']
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

        $statuses = $user->statuses()
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
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

        $this->sendEmailConfirmationTo($user);

        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }



    // 注册成功之后发激活邮件
    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }



    //  激活成功 方法
    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
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


    //显示用户关注人列表
    public function followings(User $user){
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow',compact('users','title'));
    }


    //用户显示粉丝列表
    public function followers(User $user){
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow',compact('users','title'));
    }








}
