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
        Schema::create('restaurant_food_images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('uid')->unique()->index();

            $table->uuid('food_uid')->index(); 
            $table->string('image_url'); 
            $table->string('image_type')->nullable(); // e.g. 'main', 'thumbnail', 'gallery'
            $table->integer('sort_order')->default(0); // For ordering multiple images
            $table->boolean('is_primary')->default(false); // For main display image

            $table->foreign('food_uid')
                ->references('uid')
                ->on('restaurant_foods')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_food_images');
    }
};
