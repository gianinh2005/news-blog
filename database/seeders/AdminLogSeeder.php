<?php

namespace Database\Seeders;

use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();

        if ($admin) {
            AdminLog::create([
                'user_id' => $admin->id,
                'action' => 'approve_post',
                'action_type' => 'post',
                'description' => 'Khởi tạo hệ thống và kiểm tra phân quyền quản trị.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ]);
        }
    }
}
