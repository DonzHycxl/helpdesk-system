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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('contact_email');
            $table->string('name');
            $table->dateTime('datetime_reported');
            $table->dateTime('datetime_action')->nullable();
            $table->dateTime('datetime_closed')->nullable();
            $table->dateTime('due_date');
            $table->string('subject');
            $table->text('description');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
