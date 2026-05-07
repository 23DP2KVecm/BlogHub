<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raksti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('kategorijas')->nullOnDelete();
            $table->string('virsraksts', 200)->notNull();
            $table->string('slug', 200)->unique()->notNull();
            $table->longText('saturs')->notNull();
            $table->text('ievads')->nullable();
            $table->string('attels_url')->nullable();
            $table->enum('statuss', ['melnraksts', 'publicets', 'arhivets'])->default('melnraksts');
            $table->unsignedInteger('skatijumi')->default(0);
            $table->timestamp('publicets_datums')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raksti');
    }
};
