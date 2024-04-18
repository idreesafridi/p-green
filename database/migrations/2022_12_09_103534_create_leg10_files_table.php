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
        Schema::create('leg10_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('status_leg10_id')->nullable();
            $table->foreign('status_leg10_id')->references('id')->on('status_leg10s')->onDelete('cascade')->onUpdate('cascade');

            // $table->foreignUuid('status_leg10_id')->references('id')->on('status_leg10s')->onDelete('cascade');
            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();
            $table->string('file_name')->nullable();
            $table->string('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('allow', 255)->default('admin');

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();

            $table->string('file_upload')->default(0);

            $table->string('status')->default(0);

            $table->string('bydefault')->default(0);
            $table->string('state')->default(1);

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
        Schema::dropIfExists('leg10_files');
    }
};
