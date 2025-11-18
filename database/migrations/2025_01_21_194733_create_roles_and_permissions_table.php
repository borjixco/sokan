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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('label',50)->nullable();
            $table->string('access_to',50)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('label',50)->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->unsigned();
            $table->unsignedBigInteger('role_id')->unsigned();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->unsigned();
            $table->unsignedBigInteger('user_id')->unsigned();

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
