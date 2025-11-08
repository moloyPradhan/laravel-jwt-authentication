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
        Schema::create('restaurant_menu_foods', function (Blueprint $table) {

            $table->id();
            $table->uuid('uid')->unique()->index();

            $table->uuid('restaurant_uid')->index();
            $table->uuid('menu_uid')->index();
            $table->uuid('food_uid')->index();

            // Optional: sort order within the menu
            $table->integer('sort_order')->default(0);

            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');

            $table->timestamps();

            // Foreign keys
            $table->foreign('restaurant_uid')->references('uid')->on('restaurants')->onDelete('cascade');
            $table->foreign('menu_uid')->references('uid')->on('restaurant_menus')->onDelete('cascade');
            $table->foreign('food_uid')->references('uid')->on('restaurant_foods')->onDelete('cascade');

            // Prevent duplicate menu-food mappings
            $table->unique(['menu_uid', 'food_uid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_menu_foods');
    }
};
