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
        Schema::create('pr_not_docs', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('status_pr_noti_id')->references('id')->on('status_pr_notis')->onDelete('cascade');

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->nullable();

            $table->id();

            $table->unsignedBigInteger('status_pr_noti_id')->nullable();
            $table->foreign('status_pr_noti_id')->references('id')->on('status_pr_notis')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('folder_name')->nullable();
            $table->string('description')->nullable();
            $table->string('allow', 255)->default('admin');

            $table->string('state')->nullable();

            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();

            $table->string('reminders_emails')->nullable();
            $table->string('reminders_days')->nullable();
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
        Schema::dropIfExists('pr_not_docs');
    }
};
