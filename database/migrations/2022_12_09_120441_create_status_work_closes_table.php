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
        Schema::create('status_work_closes', function (Blueprint $table) {
            // $table->UUID('id')->primary();
            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('state')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();

            $table->string('status')->default(0)->comment('1 for delete and 0 for active ');

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
        Schema::dropIfExists('status_work_closes');
    }
};
