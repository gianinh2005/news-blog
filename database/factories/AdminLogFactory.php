<?php

namespace Database\Factories;

use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminLog>
 */
class AdminLogFactory extends Factory
{
    protected $model = AdminLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => 'approve_post',
            'action_type' => 'post',
            'description' => 'Duyệt bài viết thử nghiệm',
            'properties' => ['status' => 'published'],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
