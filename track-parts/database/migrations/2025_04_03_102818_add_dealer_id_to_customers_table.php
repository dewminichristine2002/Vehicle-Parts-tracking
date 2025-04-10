<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->unsignedBigInteger('dealer_id')->nullable()->after('contact_number');
        $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropForeign(['dealer_id']);
        $table->dropColumn('dealer_id');
    });
}

};
