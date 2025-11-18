<?php

use App\Enums\UnitBlockEnum;
use App\Enums\UnitOperationEnum;
use App\Enums\UnitRoofEnum;
use App\Enums\UnitStatusEnum;
use App\Enums\UnitTypeEnum;
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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number',20)->index();
            $table->unsignedBigInteger('floor_id')->index();
            $table->foreign('floor_id')->references('id')->on('floors');
            $table->float('meterage',1)->nullable()->index(); // متراژ
            $table->enum('unit_type',enumNames(UnitTypeEnum::cases()))->index()->nullable();
            $table->string('tel',100)->nullable()->index();
            $table->string('postal_code',20)->nullable()->index();
            $table->unsignedBigInteger('position_id')->index()->nullable();
            $table->foreign('position_id')->references('id')->on('positions');
            $table->string('meter_serial_number')->nullable()->index();
            $table->bigInteger('value_per_meter')->nullable()->index();
            $table->bigInteger('total_value')->nullable()->index();
            $table->bigInteger('maximum_full_mortgage')->nullable()->index(); // حداکثر رهن کامل
            $table->bigInteger('maximum_monthly_rent')->nullable()->index(); // حداکثر اجاره ماهیانه
            $table->bigInteger('owner_annual_goodwill_rent')->nullable()->index(); // اجاره سرقفلی سالانه مالک
            $table->bigInteger('sale_price_suggested_owner')->nullable()->index(); // فروشی نرخ پیشنهادی مالک
            $table->bigInteger('owner_proposed_mortgage')->nullable()->index(); // رهن پیشنهادی مالک
            $table->bigInteger('rent_proposed_owner')->nullable()->index(); // اجاره‌ پیشنهادی مالک
            $table->bigInteger('charge_amount')->nullable()->index(); // مبلغ شارژ
            $table->string('computer_password')->nullable()->index(); // رمز رایانه
            $table->string('case')->nullable()->index(); // پرونده
            $table->enum('roof',enumNames(UnitRoofEnum::cases()))->nullable()->index();
            $table->enum('status',enumNames(UnitStatusEnum::cases()))->nullable()->index();
            $table->enum('operation',enumNames(UnitOperationEnum::cases()))->nullable()->index();
            $table->enum('block',enumNames(UnitBlockEnum::cases()))->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
