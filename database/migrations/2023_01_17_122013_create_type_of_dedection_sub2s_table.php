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
        Schema::create('type_of_dedection_sub2s', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('type_of_dedection_sub1_id')->references('id')->on('type_of_dedection_sub1s')->onDelete('cascade');


            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();

            $table->id();

            $table->unsignedBigInteger('type_of_dedection_sub1_id')->nullable();
            $table->foreign('type_of_dedection_sub1_id')->references('id')->on('type_of_dedection_sub1s')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('allow', 255)->default('admin');

            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('description')->nullable();
            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('reminders_emails')->nullable();
            $table->string('reminders_days')->nullable();
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
        Schema::dropIfExists('type_of_dedection_sub2s');
    }
};
