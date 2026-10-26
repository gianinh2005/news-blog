<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Người gửi báo cáo (null nếu khách vãng lai nếu cần, nhưng yêu cầu login)
            $table->morphs('reportable'); // reportable_type, reportable_id (Post hoặc Comment)
            $table->string('reason'); // Lý do báo cáo (từ selectbox: spam, hate_speech, copyright, violence, false_info, other)
            $table->text('description')->nullable(); // Chi tiết bổ sung
            $table->string('status')->default('pending'); // pending, resolved, dismissed
            $table->text('admin_note')->nullable(); // Ghi chú của admin khi xử lý
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
