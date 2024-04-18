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
        Schema::create('chiavetta_docs', function (Blueprint $table) {
            // $table->UUID('id')->primary();
            $table->id();

            $table->string('folder_name')->nullable();
            $table->string('allow', 255)->default('admin');

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
        Schema::dropIfExists('chiavetta_docs');
    }
};
