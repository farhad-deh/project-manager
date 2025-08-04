<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_permanent')->default(false); // true => ongoing, false => finite
            $table->integer('estimated_hours')->nullable();
            $table->integer('real_hours')->nullable();
            // extend status to allow "ongoing" as a special type
            $table->enum('status', ['todo', 'doing', 'done', 'hold', 'ongoing'])->default('todo');
            $table->integer('total_cost')->default(0);
            $table->integer('hourly_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
