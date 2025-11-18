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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // کاربری که فایل را آپلود کرده
            $table->morphs('attachable'); // مورفی: هر مدلی می‌تواند پیوست داشته باشد
            $table->string('file_path')->index(); // مسیر فایل
            $table->string('file_name')->nullable(); // نام فایل اصلی
            $table->string('mime_type')->nullable(); // نوع فایل (MIME Type)
            $table->integer('size')->nullable(); // حجم فایل به بایت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
