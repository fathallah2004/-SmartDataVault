<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_files_encrypted')->default(0);
            $table->bigInteger('total_storage_used')->default(0);
            $table->timestamp('last_upload_at')->nullable();
            $table->string('preferred_encryption')->default('aes-256');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'total_files_encrypted',
                'total_storage_used', 
                'last_upload_at',
                'preferred_encryption'
            ]);
        });
    }
};