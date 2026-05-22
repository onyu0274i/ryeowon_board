<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // post_id, user_id, content 컬럼에 데이터를 넣는 거 허용
    protected $fillable = ['post_id', 'user_id', 'content', 'like_count'];
    protected $primaryKey = 'comment_id';
    //

    // 부모 댓글과의 관계
    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // 자식 댓글(대댓글)들과의 관계
    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


