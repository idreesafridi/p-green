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
        Schema::create('works_details', function (Blueprint $table) {
            $table->id();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('window_company')->nullable();
            $table->string('window_company_prize')->nullable();

            $table->string('hybrid_system_company')->nullable();
            $table->string('hybrid_system_company_prize')->nullable();

            $table->string('electric_system_company')->nullable();
            $table->string('electric_system_company_prize')->nullable();

            $table->string('construction_system_company1')->nullable();
            $table->string('construction_system_company_prize1')->nullable();

            $table->string('construction_system_company2')->nullable();
            $table->string('construction_system_company_prize2')->nullable();

            $table->string('photovoltic')->nullable();
            $table->string('photovoltic_prize')->nullable();

            $table->string('coordinator')->nullable();
            $table->string('works_manager')->nullable();

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
        Schema::dropIfExists('works_details');
    }
};
