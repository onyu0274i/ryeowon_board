<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id('comment_id'); // 댓글 자체의 번호
            // 게시글과 연결 (게시글 번호를 저장하는 칸)
            $table->foreignId('post_id')->constrained('posts', 'posts_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id'); // 누가 썼는지
            $table->text('content'); // 댓글 내용
            $table->integer('like_count')->default(0); // 댓글 좋아요 카운트
            $table->unsignedBigInteger('parent_id')->nullable(); // 어떤 댓글의 답변인지
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};