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
        Schema::create('rel_doc_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('relief_doc_id')->nullable();
            $table->foreign('relief_doc_id')->references('id')->on('relief_docs')->onDelete('cascade')->onUpdate('cascade');


            // $table->foreignUuid('relief_doc_id')->references('id')->on('relief_docs')->onDelete('cascade');

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();

            $table->string('ref_folder_name')->nullable();
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('description')->nullable();

            $table->string('file_path')->nullable();
            $table->string('allow', 255)->default('admin');

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
        Schema::dropIfExists('rel_doc_files');
    }
};
