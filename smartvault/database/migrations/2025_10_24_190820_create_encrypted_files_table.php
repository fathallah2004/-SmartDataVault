<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('encrypted_files', function (Blueprint $table) {
            $table->id();
            
            // Informations de base du fichier
            $table->string('original_name');
            $table->integer('file_size');
            $table->string('file_type'); // pdf, txt, doc, etc.
            
            // Contenu chiffré
            $table->text('encrypted_content');
            
            // Chiffrement
            $table->string('encryption_method')->default('aes-256');
            $table->text('encryption_key'); // Clé chiffrée avec Crypt
            $table->text('iv')->nullable(); // Vecteur d'initialisation
            
            // Sécurité
            $table->string('file_hash'); // Pour vérifier l'intégrité
            
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            
            // Index simples
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('encrypted_files');
    }
};