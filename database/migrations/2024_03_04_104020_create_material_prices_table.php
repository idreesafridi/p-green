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
        Schema::create('material_prices', function (Blueprint $table) {
             // $table->UUID('id')->primary();
             $table->id();
             $table->unsignedBigInteger('material_lists_id');
             $table->foreign('material_lists_id')->references('id')->on  ('material_lists')->onDelete('cascade')->onUpdate('cascade');
             $table->unsignedBigInteger('business_detail_id');
             $table->foreign('business_detail_id')->references('id')->on('business_details')->onDelete('cascade')->onUpdate('cascade');
             $table->string('materials_price_per_unit')->nullable();
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
        Schema::dropIfExists('material_prices');
    }
};
