<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encrypted_files', function (Blueprint $table) {
            $table->id();
            
            $table->string('original_name');
            $table->integer('file_size');
            $table->string('file_type');
            $table->string('file_category')->default('text');
            
            $table->text('encrypted_content');
            
            $table->string('encryption_method')->default('aes-256');
            $table->text('encryption_key');
            $table->text('iv')->nullable();
            
            $table->string('file_hash');
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encrypted_files');
    }
};
