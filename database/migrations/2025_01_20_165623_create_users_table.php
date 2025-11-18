<?php

use App\Enums\UserGenderEnum;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->index()->nullable();
            $table->decimal('balance',40,2)->default(0)->index();
            $table->string('father_name',50)->index()->nullable();
            $table->string('national_code',14)->nullable()->index()->nullable();
            $table->string('mobile',20)->index()->unique();
            $table->string('mobile2',20)->index()->nullable();
            $table->string('email',100)->nullable()->index()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->text('avatar')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender',enumNames(UserGenderEnum::cases()))->index()->nullable();
            $table->text('address')->nullable();
            $table->string('tel',20)->index()->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
