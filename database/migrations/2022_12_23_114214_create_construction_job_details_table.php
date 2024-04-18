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
        Schema::create('construction_job_details', function (Blueprint $table) {
            // $table->uuid('id')->primary();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('fixtures')->nullable();
            $table->foreign('fixtures')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('fixtures_company_price')->nullable();

            $table->unsignedBigInteger('plumbing')->nullable();
            $table->foreign('plumbing')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('plumbing_company_price')->nullable();

            $table->unsignedBigInteger('electrical')->nullable();
            $table->foreign('electrical')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('electrical_installations_company_price')->nullable();

            $table->unsignedBigInteger('construction')->nullable();
            $table->foreign('construction')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('construction_company1_price')->nullable();

            $table->unsignedBigInteger('construction2')->nullable();
            $table->foreign('construction2')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('construction_company2_price')->nullable();

            $table->unsignedBigInteger('photovoltaic')->nullable();
            $table->foreign('photovoltaic')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('photovoltaic_price')->nullable();

            $table->unsignedBigInteger('coordinator')->nullable();
            $table->foreign('coordinator')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('construction_manager')->nullable();

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
        Schema::dropIfExists('construction_job_details');
    }
};
