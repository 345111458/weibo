<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Status extends Model{
    //
    // 多对多 绑定模型
    public function user(){

        return $this->belongsTo(User::class);
    }
}
