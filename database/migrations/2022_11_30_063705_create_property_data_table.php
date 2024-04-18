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
        Schema::create('property_data', function (Blueprint $table) {
            $table->id();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('property_street')->nullable();
            $table->string('property_house_number')->nullable();
            $table->string('property_postal_code')->nullable();
            $table->string('property_common')->nullable();
            $table->string('property_province')->nullable();
            $table->string('cadastral_dati')->nullable();
            $table->string('cadastral_section')->nullable();
            $table->string('cadastral_category')->nullable();
            $table->string('cadastral_particle')->nullable();
            $table->string('sub_ordinate')->nullable();
            $table->string('pod_code')->nullable();
            $table->string('status')->default('0');
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
        Schema::dropIfExists('property__data');
    }
};
