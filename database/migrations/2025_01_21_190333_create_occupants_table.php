<?php

use App\Enums\OccupantStatusEnum;
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
        Schema::create('occupants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->index()->constrained()->onDelete('cascade');
            $table->enum('status',enumNames(OccupantStatusEnum::cases()))->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupants');
    }
};
