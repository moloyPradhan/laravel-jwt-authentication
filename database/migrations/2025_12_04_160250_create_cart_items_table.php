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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // Public UID for cart row
            $table->string('uid', 8)->unique();

            // user OR guest UID
            $table->string('user_uid', 8)->nullable();
            $table->string('guest_uid', 8)->nullable();

            // Restaurant + Food UIDs
            $table->string('restaurant_uid', 20);
            $table->string('food_uid', 20);

            // Quantity of food
            $table->integer('quantity')->default(1);

            $table->timestamps();

            // Indexes
            $table->index('user_uid');
            $table->index('guest_uid');
            $table->index(['restaurant_uid', 'food_uid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
