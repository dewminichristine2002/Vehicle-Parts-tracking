<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('targets', function (Blueprint $table) {
        $table->unsignedBigInteger('dealer_id')->after('id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            //
        });
    }
};
