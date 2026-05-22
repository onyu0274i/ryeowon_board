<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // 기본키는 posts_id
    protected $primaryKey = 'posts_id';

    protected $fillable = [
        'user_id', 
        'category_id', 
        'title', 
        'content', 
        'view_count', 
        'like_count'
    ];

    // 게시글이 어떤 카테고리에 속해 있는지 알려주는 설정
    public function category()
    {
        // category_id를 기준으로 Category랑 연결
        return $this->belongsTo(Category::class, 'category_id');
    }

    // user 관계
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 댓글 관계
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'posts_id');
    }
}