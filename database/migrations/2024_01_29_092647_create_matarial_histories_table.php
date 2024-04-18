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
        Schema::create('matarial_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('construction_site_id');
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('construction_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('changeBy');
            $table->foreign('changeBy')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('updated_field')->nullable();
            $table->string('Original')->nullable();
            $table->string('Updated_to')->nullable();

            $table->string('reason')->nullable();
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
        Schema::dropIfExists('matarial_histories');
    }
};
