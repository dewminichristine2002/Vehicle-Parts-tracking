<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('registered_at');
        });
    }

    public function down()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};