<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    //生成用户 头像
    public function gravatar($size = '100'){

        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }


    // 生成令牌
    public static function boot(){
        parent::boot();

        static::creating(function($user){
            $user->activation_token = str_random(30);
        });
    }


    // 多对多 绑定模型
    public function statuses(){

        return $this->hasMany(Status::class);
    }


    //获取当前用户关注的人发布过的所有微博动态
    public function feed(){

        return $this->statuses()->orderBy('created_at','desc');
    }



    //通过 followers 来获取粉丝关系列表
    public function followers(){

        return $this->belongsToMany(User::class , 'followers' , 'user_id' , 'follower_id');
    }


    //通过 followings 来获取用户关注人列表
    public function followings(){

        return $this->belongsToMany(User::class , 'followers' , 'follower_id' , 'user_id');
    }


    //关注
    public function follow($user_ids){
        if (!is_array($user_ids)) {
            # code...
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }



    //取消关注
    public function unfollow($user_ids){
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }


    //我们还需要一个方法用于判断当前登录的用户 A 是否关注了用户 B
    public function isFollowing($user_ids){

        return $this->followings->contains($user_ids);
    }

}
