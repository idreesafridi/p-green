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
        Schema::create('reg_prac_docs', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('status_reg_prac_id')->references('id')->on('status_reg_pracs')->onDelete('cascade');


            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();

            $table->id();

            $table->unsignedBigInteger('status_reg_prac_id')->nullable();
            $table->foreign('status_reg_prac_id')->references('id')->on('status_reg_pracs')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('file_name')->nullable();
            $table->string('description')->nullable();
            $table->string('allow', 255)->default('admin');

            $table->string('file_path')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();

            $table->string('reminders_emails')->nullable();
            $table->string('reminders_days')->nullable();
            $table->string('file')->default(0);
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
        Schema::dropIfExists('reg_prac_docs');
    }
};
