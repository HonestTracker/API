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
        Schema::create('category_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('site_name');
            $table->string('url');
            $table->dateTime('last_crawled')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories');
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('current_price_id');
            $table->string('name');
            $table->longText('url');
            $table->decimal('current_price');
            $table->decimal('change_percentage')->nullable();
            $table->string('currency')->nullable();
            $table->string('picture_url')->nullable();
            $table->timestamps();
            $table->foreign('site_id')->references('id')->on('category_sites');

        });
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('site_id');
            $table->decimal('price');
            $table->datetime('date');
            $table->decimal('change_percentage')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('site_id')->references('id')->on('category_sites');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('current_price_id')->references('id')->on('product_prices');
        });
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('text');
            $table->decimal('stars');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
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
            $table->dropForeign('product_prices_site_id_foreign');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign('comments_product_id_foreign');
            $table->dropForeign('comments_user_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_site_id_foreign');
            $table->dropForeign('products_current_price_id_foreign');
        });
        Schema::table('category_sites', function (Blueprint $table) {
            $table->dropForeign('category_sites_category_id_foreign');
        });
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('products');
        Schema::dropIfExists('category_sites');
        Schema::dropIfExists('categories');
    }
};
