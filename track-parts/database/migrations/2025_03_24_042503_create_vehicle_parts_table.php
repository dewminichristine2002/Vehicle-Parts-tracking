<?php

// database/migrations/xxxx_xx_xx_create_vehicle_parts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclePartsTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_parts', function (Blueprint $table) {
            $table->string('part_number')->primary();
            $table->string('part_name');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_parts');
    }
};
