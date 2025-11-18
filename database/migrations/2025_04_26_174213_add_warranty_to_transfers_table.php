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
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('check_number',50)->after('duration')->nullable();
            $table->string('bank',50)->after('check_number')->nullable();
            $table->decimal('warranty_amount',20,0)->after('bank')->nullable();
            $table->string('current_account',50)->after('warranty_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['check_number','bank','warranty_amount','current_account']);
        });
    }
};
