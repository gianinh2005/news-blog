<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $post;
    protected $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'password' => bcrypt('123456')]);
        $this->user = User::factory()->create(['role' => 'user', 'is_active' => true, 'password' => bcrypt('123456')]);

        $category = Category::create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);
        $this->post = Post::create([
            'title' => 'Bài viết thử nghiệm',
            'slug' => 'bai-viet-thu-nghiem',
            'content' => 'Nội dung bài viết thử nghiệm',
            'summary' => 'Tóm tắt bài viết',
            'type' => 'news',
            'status' => 'published',
            'views' => 10,
            'category_id' => $category->id,
            'user_id' => $this->admin->id,
            'published_at' => now(),
        ]);

        $this->tag = Tag::create(['name' => 'AI', 'slug' => 'ai']);
        $this->post->tags()->attach($this->tag->id);
    }

    public function test_tag_page_accessible()
    {
        $response = $this->get('/the/' . $this->tag->slug);
        $response->assertStatus(200);
    }

    public function test_newsletter_subscription()
    {
        $email = 'test_' . uniqid() . '@example.com';
        $response = $this->post('/newsletter/subscribe', ['email' => $email]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => $email]);
    }

    public function test_bookmark_toggle()
    {
        $response = $this->actingAs($this->user)->postJson('/bai-viet/' . $this->post->id . '/bookmark');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_follow_author_toggle()
    {
        $response = $this->actingAs($this->user)->postJson('/tac-gia/' . $this->admin->id . '/follow');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_nested_comment_reply()
    {
        // Tạo comment gốc
        $rootComment = Comment::create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Bình luận gốc thử nghiệm'
        ]);

        // Gửi reply lồng nhau
        $response = $this->actingAs($this->admin)->post('/bai-viet/' . $this->post->id . '/comments', [
            'content' => 'Trả lời bình luận gốc',
            'parent_id' => $rootComment->id
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'parent_id' => $rootComment->id,
            'content' => 'Trả lời bình luận gốc'
        ]);
    }

    public function test_notifications_page()
    {
        $response = $this->actingAs($this->user)->get('/thong-bao');
        $response->assertStatus(200);
    }

    public function test_profile_saved_and_following_tabs()
    {
        $responseSaved = $this->actingAs($this->user)->get('/ho-so?tab=saved');
        $responseSaved->assertStatus(200);

        $responseFollowing = $this->actingAs($this->user)->get('/ho-so?tab=following');
        $responseFollowing->assertStatus(200);
    }

    public function test_admin_dashboard_and_exports()
    {
        $dash = $this->actingAs($this->admin)->get('/admin');
        $dash->assertStatus(200);

        $subs = $this->actingAs($this->admin)->get('/admin/subscribers');
        $subs->assertStatus(200);

        $exportPosts = $this->actingAs($this->admin)->get('/admin/export/posts');
        $exportPosts->assertStatus(200);

        $exportUsers = $this->actingAs($this->admin)->get('/admin/export/users');
        $exportUsers->assertStatus(200);

        $exportReports = $this->actingAs($this->admin)->get('/admin/export/reports');
        $exportReports->assertStatus(200);
    }
}
