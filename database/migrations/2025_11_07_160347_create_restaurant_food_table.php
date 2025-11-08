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
        Schema::create('restaurant_foods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique()->index();
            $table->uuid('restaurant_uid')->index();

            $table->string('name');
            $table->string('slug');
            $table->string('code', 10)->unique();
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('currency', 10)->default('INR');

            $table->boolean('is_veg')->default(true);
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time')->nullable();

            $table->json('tags')->nullable();
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('restaurant_uid')->references('uid')->on('restaurants')->onDelete('cascade');

            $table->unique(['restaurant_uid', 'slug']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_food');
    }
};
