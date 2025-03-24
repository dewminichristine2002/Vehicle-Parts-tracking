<?php

// database/migrations/xxxx_xx_xx_create_other_costs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherCostsTable extends Migration
{
    public function up()
    {
        Schema::create('other_costs', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->string('invoice_no');
            $table->timestamps();

            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('other_costs');
    }
};