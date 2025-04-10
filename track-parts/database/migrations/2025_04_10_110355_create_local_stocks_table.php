<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('local_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id');
            $table->unsignedBigInteger('global_part_id');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            $table->foreign('global_part_id')->references('id')->on('global_parts')->onDelete('cascade');
            $table->unique(['dealer_id', 'global_part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_stocks');
    }
};
