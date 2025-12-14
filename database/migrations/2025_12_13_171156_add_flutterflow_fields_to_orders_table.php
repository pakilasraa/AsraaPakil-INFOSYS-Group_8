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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_phone')->nullable()->after('customer_name');
        $table->string('address')->nullable()->after('customer_phone');
        $table->json('items')->nullable()->after('status');

        // Optional safety: make firebase_uid nullable for FlutterFlow orders
        $table->string('firebase_uid')->nullable()->change();
    });
}

};
