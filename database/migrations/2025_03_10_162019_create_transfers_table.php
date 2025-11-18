<?php

use App\Enums\TransferTypeEnum;
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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->index()->constrained('units')->onDelete('cascade');
            $table->string('lawyer',100)->nullable()->index();
            $table->string('reagent',100)->nullable()->index();
            $table->string('regulator',100)->nullable()->index();
            $table->string('contract_number')->nullable()->index();
            $table->decimal('ownership',20,0)->index()->nullable(); // حق مالکانه
            $table->decimal('cost',20,0)->index()->nullable(); // حق مالکانه
            $table->decimal('goodwill_rental',20,0)->index()->nullable(); // اجاره سرقفلی
            $table->string('first_witness')->nullable(); // شاهد اول
            $table->string('second_witness')->nullable(); // شاهد دوم
            $table->text('terms')->nullable();
            $table->decimal('mortgage_amount',20,0)->nullable()->index();
            $table->decimal('rental_amount',20,0)->nullable()->index();
            $table->string('duration',100)->nullable();
            $table->enum('type',enumNames(TransferTypeEnum::cases()))->index();
            $table->date('rental_start_at')->nullable()->index();
            $table->date('rental_end_at')->nullable()->index();
            $table->date('doing_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
