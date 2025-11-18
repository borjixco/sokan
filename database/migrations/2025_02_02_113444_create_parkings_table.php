<?php

use App\Enums\ParkingStatusEnum;
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
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade'); // واحد
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // پرداخت کننده
            $table->foreignId('car_id')->constrained()->onDelete('cascade'); // ماشین
            $table->date('start_date')->index(); // شروع اشتراک
            $table->date('end_date')->index(); // پایان اشتراک
            $table->decimal('amount', 20, 0)->index()->default(0); // مبلغ
            $table->enum('status', enumNames(ParkingStatusEnum::cases()))->index()->default('unpaid'); // وضعیت
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
