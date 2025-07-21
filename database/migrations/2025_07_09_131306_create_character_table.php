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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('firstName');
            $table->string('secondName');
            $table->string('gender');
            $table->integer('age');
            $table->integer('height');
            $table->integer('weight');
            $table->string('nationality');
            $table->string('residentialAddress');
            $table->string('activity');
            $table->text('personality');
            $table->integer('available_points')->default(0);

            $table->foreignId('status_id')->constrained('character_statuses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
