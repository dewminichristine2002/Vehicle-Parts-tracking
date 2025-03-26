<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sold_parts', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->after('unit_price');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('sold_parts', function (Blueprint $table) {
        $table->dropColumn('discount');
    });
}

};
