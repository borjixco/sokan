<?php

use App\Enums\TicketStatusEnum;
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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade'); // کاربر ثبت‌کننده تیکت
            $table->string('subject')->index(); // موضوع تیکت
            $table->enum('status', enumNames(TicketStatusEnum::cases()))->index()->default('PENDING')->index(); // وضعیت تیکت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
