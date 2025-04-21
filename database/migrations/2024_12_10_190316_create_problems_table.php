<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Untitled Problem');
            $table->string('type')->default('Other'); // Default type to avoid null values
            $table->text('description')->nullable();
            $table->json('problem_attachments')->nullable();
            $table->string('status')->default('hidden'); // Default status to avoid null
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('delete_reason')->nullable();
            $table->softDeletes(); // Enables soft deletes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
