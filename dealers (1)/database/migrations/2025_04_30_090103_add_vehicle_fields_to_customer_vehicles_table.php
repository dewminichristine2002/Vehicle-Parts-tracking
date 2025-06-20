<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_vehicles', function (Blueprint $table) {
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('odo')->nullable(); // odometer reading
            $table->string('type')->nullable(); // vehicle type
        });
    }
    
    public function down()
    {
        Schema::table('customer_vehicles', function (Blueprint $table) {
            $table->dropColumn(['make', 'model', 'odo', 'type']);
        });
    }
    
};
