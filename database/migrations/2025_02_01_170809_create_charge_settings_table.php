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
        Schema::create('charge_settings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->bigInteger('base_amount'); // مبلغ کلی شارژ واحدهای خالی
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_settings');
    }
};
