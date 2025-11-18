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
        Schema::create('job_typeables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_type_id')->constrained()->onDelete('cascade');
            $table->morphs('job_typeable'); // job_typeable_id, job_typeable_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_typeables');
    }
};
