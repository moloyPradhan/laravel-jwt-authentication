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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->string('uid', 8);     
            $table->string('order_uid', 8);     
            $table->string('food_uid', 8);   

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);    
            $table->decimal('total', 10, 2);    

            $table->timestamps();

            $table->foreign('order_uid')
                ->references('uid')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('food_uid')
                ->references('uid')
                ->on('restaurant_foods')
                ->onDelete('cascade');

            $table->index(['order_uid', 'food_uid']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
