<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void {
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->string('contact_number', 10);
            $table->string('vehicle_number');

            $table->primary(['contact_number', 'vehicle_number']);

            $table->foreign('contact_number')
                  ->references('contact_number')
                  ->on('customers')
                  ->onUpdate('restrict') // prevent auto-updating contact
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('customer_vehicles');
    }
};
