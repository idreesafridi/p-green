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
        Schema::create('materials_asisstances', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');
            // $table->foreignUuid('construction_material_id')->nullable()->references('id')->on('construction_materials')->onDelete('cascade');

            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_material_id')->nullable();
            $table->foreign('construction_material_id')->references('id')->on('construction_materials')->onDelete('cascade')->onUpdate('cascade');

            $table->string('machine_model')->nullable();
            $table->string('freshman')->nullable();
            $table->string('start_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('invoice')->nullable();
            $table->string('report')->nullable();
            $table->string('notes')->default('Assitenza regolare')->nullable();
            $table->string('state')->default('To complete')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status')->default(0);

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
        Schema::dropIfExists('materials_asisstances');
    }
};
