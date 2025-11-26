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
        Schema::create('menu_food', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique()->index();
            $table->char('menu_id', 36);
            $table->char('food_id', 36);

            $table->foreign('menu_id')->references('uid')->on('restaurant_menus')->onDelete('cascade');
            $table->foreign('food_id')->references('uid')->on('restaurant_foods')->onDelete('cascade');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_food');
    }
};
