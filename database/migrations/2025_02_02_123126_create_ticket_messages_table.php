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
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->index()->constrained()->onDelete('cascade'); // مرتبط با یک تیکت
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade'); // نویسنده پیام
            $table->text('message'); // متن پیام
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
