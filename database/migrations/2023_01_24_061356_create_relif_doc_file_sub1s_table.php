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
        Schema::create('relif_doc_file_sub1s', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('rel_doc_file_id')->references('id')->on('rel_doc_files')->onDelete('cascade');

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();

            $table->id();

            $table->unsignedBigInteger('rel_doc_file_id')->nullable();
            $table->foreign('rel_doc_file_id')->references('id')->on('rel_doc_files')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('rel_doc_file_folder_name')->nullable();
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('allow', 255)->default('admin');

            $table->string('state')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();

            $table->string('reminders_emails')->nullable();
            $table->string('reminders_days')->nullable();

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
        Schema::dropIfExists('relif_doc_file_sub1s');
    }
};
