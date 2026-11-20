<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('icebox_code')->unique(); // 2, 3, 8 и т.д. как на главной
            $table->string('name'); // Крытый каток «Олимпиец» — ледовая арена
            $table->enum('section', ['ice', 'hall', 'foyer']); // Аренда льда / помещений / фойе
            $table->string('type_label'); // Ледовая арена, Зал, Бассейн...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};