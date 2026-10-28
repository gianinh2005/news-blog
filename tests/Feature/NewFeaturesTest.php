<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReaction;
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
        $response = $this->get('/the/'.$this->tag->slug);
        $response->assertStatus(200);
    }

    public function test_newsletter_subscription()
    {
        $email = 'test_'.uniqid().'@example.com';
        $response = $this->post('/newsletter/subscribe', ['email' => $email]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => $email]);
    }

    public function test_bookmark_toggle()
    {
        $response = $this->actingAs($this->user)->postJson('/bai-viet/'.$this->post->id.'/bookmark');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_follow_author_toggle()
    {
        $response = $this->actingAs($this->user)->postJson('/tac-gia/'.$this->admin->id.'/follow');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_nested_comment_reply()
    {
        // Tạo comment gốc
        $rootComment = Comment::create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Bình luận gốc thử nghiệm',
        ]);

        // Gửi reply lồng nhau
        $response = $this->actingAs($this->admin)->post('/bai-viet/'.$this->post->id.'/comments', [
            'content' => 'Trả lời bình luận gốc',
            'parent_id' => $rootComment->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'parent_id' => $rootComment->id,
            'content' => 'Trả lời bình luận gốc',
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

        $usersPage = $this->actingAs($this->admin)->get('/admin/users');
        $usersPage->assertStatus(200);
    }

    public function test_get_reaction_users_list()
    {
        PostReaction::create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'type' => 'love',
        ]);

        $response = $this->getJson('/bai-viet/'.$this->post->id.'/reactions/users');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('counts.love', 1)
            ->assertJsonPath('reactions.0.user_name', $this->user->name)
            ->assertJsonPath('reactions.0.reaction_type', 'love');

        // Lọc theo type=love
        $responseLove = $this->getJson('/bai-viet/'.$this->post->id.'/reactions/users?type=love');
        $responseLove->assertStatus(200)
            ->assertJsonCount(1, 'reactions');

        // Lọc theo type=haha (không có ai)
        $responseHaha = $this->getJson('/bai-viet/'.$this->post->id.'/reactions/users?type=haha');
        $responseHaha->assertStatus(200)
            ->assertJsonCount(0, 'reactions');
    }

    public function test_only_admin_at_gmail_can_change_role()
    {
        $targetUser = User::factory()->create(['role' => 'user', 'is_active' => true]);

        // Tài khoản admin@gmail.com được phép đổi role
        $superAdmin = User::factory()->create(['email' => 'admin@gmail.com', 'role' => 'admin', 'is_active' => true]);
        $responseSuccess = $this->actingAs($superAdmin)->patch('/admin/users/'.$targetUser->id, [
            'role' => 'admin',
        ]);
        $responseSuccess->assertRedirect();
        $this->assertEquals('admin', $targetUser->fresh()->role);

        // Kiểm tra load trang /admin/users với tài khoản admin@gmail.com
        $this->actingAs($superAdmin)->get('/admin/users')->assertStatus(200);

        // Tài khoản admin khác (không phải admin@gmail.com) bị chặn đổi role với lỗi 403
        $otherAdmin = User::factory()->create(['email' => 'other_admin@example.com', 'role' => 'admin', 'is_active' => true]);
        $responseForbidden = $this->actingAs($otherAdmin)->patch('/admin/users/'.$targetUser->id, [
            'role' => 'user',
        ]);
        $responseForbidden->assertStatus(403);
        $this->assertEquals('admin', $targetUser->fresh()->role);

        // Kiểm tra load trang /admin/users với tài khoản admin khác (xem danh sách bình thường)
        $this->actingAs($otherAdmin)->get('/admin/users')->assertStatus(200);
    }

    public function test_admin_logs_recorded_on_post_approve_and_comment_delete()
    {
        // Thử nghiệm duyệt bài viết
        $pendingPost = Post::create([
            'title' => 'Bài viết chờ duyệt',
            'slug' => 'bai-viet-cho-duyet',
            'content' => 'Nội dung chờ duyệt',
            'type' => 'blog',
            'status' => 'pending',
            'category_id' => $this->post->category_id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin)->patch('/admin/posts/'.$pendingPost->id.'/approve');
        $this->assertDatabaseHas('admin_logs', [
            'action' => 'approve_post',
            'action_type' => 'post',
            'user_id' => $this->admin->id,
        ]);

        // Thử nghiệm xóa bình luận
        $comment = Comment::create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Bình luận cần xóa',
        ]);

        $this->actingAs($this->admin)->delete('/admin/comments/'.$comment->id);
        $this->assertDatabaseHas('admin_logs', [
            'action' => 'delete_comment',
            'action_type' => 'comment',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_logs_page_accessible_and_clear_restricted()
    {
        // Truy cập trang danh sách nhật ký
        $response = $this->actingAs($this->admin)->get('/admin/logs');
        $response->assertStatus(200);

        // Lọc theo action_type=post
        $responseFiltered = $this->actingAs($this->admin)->get('/admin/logs?action_type=post');
        $responseFiltered->assertStatus(200);

        // Admin thông thường (không phải admin@gmail.com) không có quyền dọn dẹp log
        $responseForbidden = $this->actingAs($this->admin)->delete('/admin/logs/clear');
        $responseForbidden->assertStatus(403);

        // Tài khoản admin@gmail.com được phép dọn dẹp log
        $superAdmin = User::factory()->create(['email' => 'admin@gmail.com', 'role' => 'admin', 'is_active' => true]);
        $responseSuccess = $this->actingAs($superAdmin)->delete('/admin/logs/clear', ['days' => 30]);
        $responseSuccess->assertRedirect();
    }
}
