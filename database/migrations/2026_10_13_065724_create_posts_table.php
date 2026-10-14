<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable(); // Tóm tắt ngắn
            $table->longText('content');         // Nội dung chi tiết
            $table->string('thumbnail')->nullable(); // Ảnh bài viết
            $table->enum('type', ['news', 'blog'])->default('blog'); // Phân loại tin tức hoặc blog
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
