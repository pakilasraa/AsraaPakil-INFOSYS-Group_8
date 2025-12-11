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
    Schema::table('order_product', function (Blueprint $table) {
        $table->string('temperature')->nullable(); // Add temperature field
    });
}

public function down()
{
    Schema::table('order_product', function (Blueprint $table) {
        $table->dropColumn('temperature'); // Remove temperature if rolled back
    });
}

};
