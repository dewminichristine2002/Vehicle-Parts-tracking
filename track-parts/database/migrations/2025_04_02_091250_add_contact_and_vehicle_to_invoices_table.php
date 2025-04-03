<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->string('contact_number')->nullable();
        $table->string('vehicle_number')->nullable();
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn(['contact_number', 'vehicle_number']);
    });
}

};
