<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tagNames = [
            'Trí tuệ nhân tạo (AI)',
            'Laravel & PHP',
            'Lập trình Web',
            'An ninh mạng',
            'Lối sống xanh',
            'Ẩm thực Việt Nam',
            'Du lịch & Trải nghiệm',
            'Esports Việt Nam',
            'Tài chính cá nhân',
            'Kỹ năng làm việc',
            'Đọc sách mỗi ngày',
            'Mẹo công nghệ',
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $tags[] = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // Attach 2-3 random tags to each existing published post
        $allPosts = Post::all();
        foreach ($allPosts as $post) {
            if ($post->tags()->count() === 0) {
                $randomTagIds = collect($tags)->random(rand(2, 3))->pluck('id');
                $post->tags()->syncWithoutDetaching($randomTagIds);
            }
        }
    }
}
