<?php

use App\Enums\ContractStatusEnum;
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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title',100)->index();
            $table->string('company')->nullable()->index();
            $table->decimal('cost', 20,0)->nullable()->index();
            $table->string('terms')->nullable();
            $table->string('guarantee')->nullable();
            $table->string('tel',20)->nullable();
            $table->text('description')->nullable();
            $table->date('start_at')->nullable()->index();
            $table->date('end_at')->nullable()->index();
            //$table->enum('status', enumNames(ContractStatusEnum::cases()))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
