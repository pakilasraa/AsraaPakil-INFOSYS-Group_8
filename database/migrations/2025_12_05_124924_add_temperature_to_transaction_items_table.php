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
    Schema::table('transaction_items', function (Blueprint $table) {
        // temperature: 'hot' or 'iced' (nullable para sa luma na orders)
        $table->string('temperature', 10)->nullable()->after('price');
    });
}

public function down()
{
    Schema::table('transaction_items', function (Blueprint $table) {
        $table->dropColumn('temperature');
    });
}

};
