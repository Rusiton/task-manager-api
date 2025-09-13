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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('token');

            $table->foreignId('column_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('position');
            $table->date('due_date')->nullable();
            
            $table->unique(['column_id', 'position']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
