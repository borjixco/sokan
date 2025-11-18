<?php

use App\Enums\CarStatusEnum;
use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade'); // واحد
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // پرداخت کننده
            $table->decimal('amount', 20, 0)->index(); // مبلغ
            $table->enum('payment_method', enumNames(ChargePaymentMethodEnum::cases()))->nullable()->index();
            $table->enum('status', enumNames(ChargeStatusEnum::cases()))->index()->default('UNPAID'); // وضعیت
            $table->tinyInteger('reminder')->nullable()->default(0);
            $table->date('period')->index()->nullable();
            $table->dateTime('due_date')->index()->nullable(); // مهلت پرداخت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
