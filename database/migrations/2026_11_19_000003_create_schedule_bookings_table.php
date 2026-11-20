<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedule_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time'); // кратно 15 минутам (06:00, 06:15...)
            $table->unsignedInteger('duration_minutes'); // 15, 30, 45, 60, 90...
            $table->boolean('is_ice_prep')->default(false); // Чёрная ячейка «Подготовка / Заливка льда»
            $table->string('tenant_name')->nullable(); // Арендатор (команда, тренер, раздевалки)
            $table->string('category')->default('other'); // льготная, фигурное, хоккей, вхл, коммерческая, ночная, другое
            $table->string('tenant_email')->nullable();
            $table->string('tenant_phone')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_bookings');
    }
};