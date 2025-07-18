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
            $table->string('nationality');
            $table->string('residentialAddress');
            $table->string('activity');
            $table->text('personality')->nullable();
            $table->text('biography')->nullable();
            $table->text('description')->nullable();

            $table->integer('total_points')->default(6);
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
