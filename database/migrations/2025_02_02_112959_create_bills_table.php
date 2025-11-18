<?php

use App\Enums\BillStatusEnum;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // پرداخت کننده
            $table->string('title')->index()->nullable(); // موضوع قبض
            $table->decimal('amount', 20, 0)->index()->default(0); // مبلغ
            $table->enum('status', enumNames(BillStatusEnum::cases()))->index()->default('unpaid'); // وضعیت
            $table->date('due_date')->index()->nullable(); // مهلت پرداخت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
