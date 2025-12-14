<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('order_product', function (Blueprint $table) {

        if (!Schema::hasColumn('order_product', 'size')) {
            $table->string('size')->nullable()->after('quantity');
        }

        if (!Schema::hasColumn('order_product', 'temperature')) {
            $table->string('temperature')->nullable()->after('size');
        }

        if (!Schema::hasColumn('order_product', 'unit_price')) {
            $table->decimal('unit_price', 10, 2)->nullable()->after('temperature');
        }
    });
}

public function down(): void
{
    Schema::table('order_product', function (Blueprint $table) {
        if (Schema::hasColumn('order_product', 'unit_price')) {
            $table->dropColumn('unit_price');
        }
        if (Schema::hasColumn('order_product', 'temperature')) {
            $table->dropColumn('temperature');
        }
        if (Schema::hasColumn('order_product', 'size')) {
            $table->dropColumn('size');
        }
    });
}
};
