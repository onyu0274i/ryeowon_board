<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create(['category_name' => '자유게시판']);
        \App\Models\Category::create(['category_name' => '공지사항']);
        \App\Models\Category::create(['category_name' => '비밀게시판']);
    }
}
