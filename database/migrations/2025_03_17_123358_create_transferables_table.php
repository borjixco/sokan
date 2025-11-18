<?php

use App\Enums\TransferableTypeEnum;
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
        Schema::create('transferables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->onDelete('cascade');
            $table->morphs('transferable'); // این فیلدها می‌توانند به owner یا occupant اشاره کنند
            $table->enum('type',enumNames(TransferableTypeEnum::cases()))->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferables');
    }
};
