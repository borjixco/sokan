<?php

use App\Enums\OwnerStatusEnum;
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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->index()->constrained()->onDelete('cascade');
            $table->double('quota')->index()->nullable();
            $table->enum('status',enumNames(OwnerStatusEnum::cases()))->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
