<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('construction_shipping_lists', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('construction_shipping_id')->references('id')->on('construction_shippings')->onDelete('cascade')->nullable();
            // $table->foreignUuid('construction_material_id')->references('id')->on('construction_materials')->onDelete('cascade')->nullable();

            $table->id();

            $table->unsignedBigInteger('construction_shipping_id')->nullable();
            $table->foreign('construction_shipping_id')->references('id')->on('construction_shippings')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_material_id')->nullable();
            $table->foreign('construction_material_id')->references('id')->on('construction_materials')->onDelete('cascade')->onUpdate('cascade');

            $table->string('qty')->nullable();
            $table->string('rem_qty')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('ship_change')->nullable();

            $table->string('shipping_truck')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('construction_site_shipping_lists');
    }
};
