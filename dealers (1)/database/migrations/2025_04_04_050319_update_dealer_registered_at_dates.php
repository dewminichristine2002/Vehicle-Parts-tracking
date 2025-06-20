<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dateTime('registered_at')->change();
        });
        
        // Update existing string dates to proper datetime format
        \App\Models\Dealer::chunk(200, function ($dealers) {
            foreach ($dealers as $dealer) {
                if (is_string($dealer->registered_at)) {
                    $dealer->update([
                        'registered_at' => \Carbon\Carbon::parse($dealer->registered_at)
                    ]);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('registered_at')->change();
        });
    }
};