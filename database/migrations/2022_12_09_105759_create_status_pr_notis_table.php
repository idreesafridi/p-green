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
        Schema::create('status_pr_notis', function (Blueprint $table) {
            // $table->UUID('id')->primary();
            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');
            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->string('state')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('reminders_emails')->default(1);
            $table->string('reminders_days')->default(3);
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
        Schema::dropIfExists('status_pr_notis');
    }
};
