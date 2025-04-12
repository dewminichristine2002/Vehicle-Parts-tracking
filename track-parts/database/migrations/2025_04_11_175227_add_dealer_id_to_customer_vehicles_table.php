<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('customer_vehicles', function (Blueprint $table) {
        $table->unsignedBigInteger('dealer_id')->nullable()->after('vehicle_number');

        $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');

        $table->unique(['dealer_id', 'contact_number', 'vehicle_number'], 'unique_vehicle_per_dealer');
    });
}

public function down(): void
{
    Schema::table('customer_vehicles', function (Blueprint $table) {
        $table->dropUnique('unique_vehicle_per_dealer');
        $table->dropForeign(['dealer_id']);
        $table->dropColumn('dealer_id');
    });
}

};


