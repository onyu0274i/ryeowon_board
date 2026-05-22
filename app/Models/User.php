<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. 기본키 설정
    protected $primaryKey = 'user_id';

    // 2. 대량 입력 허용
    protected $fillable = [
        'user_login_id', 
        'nickname', 
        'password', 
        'email',
    ];

    // 3. 화면에 숨길 정보
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 4. 관계 설정: 사용자는 여러 게시글을 가질 수 있음
    public function posts()
    {       
        return $this->hasMany(Post::class, 'user_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}