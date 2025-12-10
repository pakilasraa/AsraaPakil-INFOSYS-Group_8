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
    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('firebase_uid')->nullable()->unique(); // link sa Firebase user
        $table->string('name')->nullable();
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        // optional pa:
        // $table->string('address')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
