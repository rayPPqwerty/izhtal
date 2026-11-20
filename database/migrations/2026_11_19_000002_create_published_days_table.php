<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('published_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['facility_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('published_days');
    }
};