<?php

use App\Enums\TransactionMethodEnum;
use App\Enums\TransactionStatusEnum;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->nullableMorphs('payable'); // اتصال به قبض، شارژ یا پارکینگ
            $table->decimal('amount', 20, 0)->default(0)->index();
            $table->enum('method', enumNames(TransactionMethodEnum::cases()))->index();
            $table->enum('status', enumNames(TransactionStatusEnum::cases()))->default('pending')->index();
            $table->string('gateway',50)->nullable()->index();
            $table->uuid()->index()->nullable(); // شناسه یکتا برای پیگیری تراکنش و وریفای تراکنش
            $table->string('transaction_id')->index()->nullable(); // شناسه تراکنش داخلی
            $table->string('reference_id')->index()->nullable(); //شماره مرجع بانکی
            $table->string('trace_number')->index()->nullable(); // شماره پیگیری بانک
            $table->text('description')->nullable();
            $table->string('card_number',50)->nullable();
            $table->dateTime('paid_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
