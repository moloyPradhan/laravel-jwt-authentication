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
        Schema::create('payment_gateway', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 8)->unique();
            $table->string('order_id', 100)->unique();
            $table->string('payment_id', 100)->unique();

            $table->text('request');      // changed
            $table->text('response');     // changed

            $table->text('success_action');
            $table->text('failed_action');
            $table->string('status', 50);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway');
    }
};
