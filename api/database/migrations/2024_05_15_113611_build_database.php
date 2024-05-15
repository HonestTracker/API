<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('site_name');
            $table->string('category_id');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories');
        });
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('price');
            $table->date('date');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropForeign('product_prices_product_id_foreign');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign('comments_user_id_foreign');
        });
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
