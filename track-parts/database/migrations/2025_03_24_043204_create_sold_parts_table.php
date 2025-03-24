<?php

// database/migrations/xxxx_xx_xx_create_sold_parts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldPartsTable extends Migration
{
    public function up()
    {
        Schema::create('sold_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number'); // NOT a foreign key
            $table->string('part_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('invoice_no');
            $table->timestamps();

            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sold_parts');
    }
};