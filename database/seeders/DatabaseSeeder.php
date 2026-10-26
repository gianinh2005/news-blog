<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản Trị Viên',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'bio' => 'Quản trị viên & Tác giả chính của website tin tức và blog.',
            ]
        );

        // 2. Tạo tài khoản độc giả mẫu
        $reader = User::firstOrCreate(
            ['email' => 'reader@gmail.com'],
            [
                'name' => 'Nguyễn Văn A',
                'password' => Hash::make('123'),
                'role' => 'user',
            ]
        );

        // 3. Tạo danh mục bài viết
        $categoriesData = [
            [
                'name' => 'Thời sự & Xã hội',
                'description' => 'Tin tức thời sự cập nhật mới nhất trong ngày.',
            ],
            [
                'name' => 'Công nghệ & Lập trình',
                'description' => 'Xu hướng công nghệ, AI và kiến thức phát triển phần mềm.',
            ],
            [
                'name' => 'Góc Blog & Trải nghiệm',
                'description' => 'Góc nhìn cá nhân, nhật ký và những câu chuyện cuộc sống thường nhật.',
            ],
            [
                'name' => 'Đọc sách & Kỹ năng',
                'description' => 'Review sách hay và rèn luyện các kỹ năng mềm.',
            ],
        ];

        $sampleImages = [
            'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?w=800&auto=format&fit=crop&q=80',
        ];

        foreach ($categoriesData as $index => $catItem) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($catItem['name'])],
                [
                    'name' => $catItem['name'],
                    'description' => $catItem['description'],
                ]
            );

            // Tạo 2 bài viết mẫu cho mỗi danh mục
            $isNews = Str::contains($category->name, 'Thời sự');

            for ($i = 1; $i <= 2; $i++) {
                $title = ($isNews ? 'Bản tin số ' : 'Tâm sự blog: ').$category->name." tập {$i}";
                $post = Post::create([
                    'user_id' => $admin->id,
                    'category_id' => $category->id,
                    'title' => $title,
                    'slug' => Str::slug($title).'-'.uniqid(),
                    'summary' => "Bài viết tóm tắt góc nhìn và cập nhật thông tin nổi bật trong danh mục {$category->name}.",
                    'content' => "Chào mừng bạn đến với bài viết chi tiết!\n\nWebsite này là sự kết hợp giữa hệ thống Tin tức nóng hổi và Blog cá nhân, cho phép người đọc theo dõi thông tin cập nhật cũng như các bài viết chia sẻ chiều sâu.\n\nLaravel cung cấp kiến trúc Eloquent ORM và Blade Template vô cùng linh hoạt giúp bạn mở rộng thêm tính năng như tag bài viết, upload ảnh thư viện, đính kèm đa phương tiện...",
                    'thumbnail' => null,
                    'type' => $isNews ? 'news' : 'blog',
                    'status' => 'published',
                    'views' => rand(25, 450),
                    'published_at' => now()->subDays(rand(0, 5)),
                ]);

                // Tạo bình luận mẫu
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $reader->id,
                    'content' => 'Bài viết rất hay và hữu ích, cảm ơn tác giả đã chia sẻ!',
                ]);
            }
        }
    }
}
