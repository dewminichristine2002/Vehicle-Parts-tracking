<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalToOtherCostsTable extends Migration
{
    public function up()
    {
        Schema::table('other_costs', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->after('discount')->default(0);
        });
    }

    public function down()
    {
        Schema::table('other_costs', function (Blueprint $table) {
            $table->dropColumn('total');
        });
    }
}
