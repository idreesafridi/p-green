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
        Schema::create('status_pre_analyses', function (Blueprint $table) {
            $table->id();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->foreignId('construction_site_id')->constrained('construction_sites')->cascadeOnDelete()->cascadeOnUpdate();

            // $table->unsignedBigInteger('construction_site_id')->nullable();
            // $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('state')->default('To be invoiced')->nullable();
            $table->string('turnover')->nullable();
            $table->string('embedded')->nullable();
            $table->string('updated_on')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('latest_status')->nullable();
            $table->string('reminders_emails')->default(5);
            // $table->string('reminders_emails')->nullable();
            $table->string('reminders_days')->default(10);
            // $table->string('reminders_days')->nullable();
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
        Schema::dropIfExists('status_pre_analyses');
    }
};
