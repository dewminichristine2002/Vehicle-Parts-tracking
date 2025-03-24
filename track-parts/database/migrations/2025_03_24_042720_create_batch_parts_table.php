<?php

// database/migrations/xxxx_xx_xx_create_batch_parts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchPartsTable extends Migration
{
    public function up()
    {
        Schema::create('batch_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number'); // NOT a foreign key
            $table->string('part_name');
            $table->integer('quantity');
            $table->string('batch_no'); // FK to batches
            $table->timestamps();

            $table->foreign('batch_no')->references('batch_no')->on('batches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('batch_parts');
    }
};