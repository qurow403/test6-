<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 認証機能付与のため追加
use Illuminate\Foundation\Auth\User as Authenticatable;

// 認証機能付与のためModel → Authenticatable に変更
class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password'
    ];
}
