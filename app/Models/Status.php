<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model{
    //

    // 多对多 绑定
    public function user(){

        return $this->belongsTo(User::class);
    }
}
