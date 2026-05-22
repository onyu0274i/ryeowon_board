<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 카테고리만 생성
        Category::create(['category_name' => '자유게시판']);
        Category::create(['category_name' => '공지사항']);
        Category::create(['category_name' => '비밀게시판']);
    }
}