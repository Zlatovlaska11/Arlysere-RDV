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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type'); // rdv, collective, evenement
            $table->integer('count')->default(1);
            $table->boolean('was_here_before')->default(false);
            $table->integer('duration')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('options')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('options')->nullOnDelete();
            $table->string('material')->nullable(); // notebook, phone, tablet, mix
            $table->foreignId('theme_id')->nullable()->constrained('options')->nullOnDelete();
            $table->foreignId('difficulty_id')->nullable()->constrained('options')->nullOnDelete();
            $table->string('gender')->nullable();
            $table->foreignId('age_id')->nullable()->constrained('options')->nullOnDelete();
            $table->foreignId('statut_id')->nullable()->constrained('options')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
