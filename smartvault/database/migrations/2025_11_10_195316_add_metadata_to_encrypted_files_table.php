<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('encrypted_files', function (Blueprint $table) {
            $table->text('metadata')->nullable()->after('file_hash');
        });
    }

    public function down()
    {
        Schema::table('encrypted_files', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
