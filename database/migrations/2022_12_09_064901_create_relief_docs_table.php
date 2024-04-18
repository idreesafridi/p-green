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
        Schema::create('relief_docs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');


            $table->unsignedBigInteger('status_relief_id')->nullable();
            $table->foreign('status_relief_id')->references('id')->on('status_reliefs')->onDelete('cascade')->onUpdate('cascade');


            // $table->foreignUuid('status_relief_id')->references('id')->on('status_reliefs')->onDelete('cascade');

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->string('allow', 255)->default('admin');


            $table->string('folder_name')->nullable();
            $table->string('description')->nullable();
            $table->string('state')->default(1);

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status')->default(1);

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
        Schema::dropIfExists('relief_docs');
    }
};
