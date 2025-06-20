<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('company_address')->nullable();
            $table->string('company_mobile')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('owner_mobile')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_designation')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_contact')->nullable();
        });
    }

    public function down()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn([
                'company_address',
                'company_mobile',
                'company_email',
                'company_logo',
                'owner_mobile',
                'user_name',
                'user_designation',
                'user_email',
                'user_contact'
            ]);
        });
    }
};