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
        Schema::create('material_types', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('material_option_id')->nullable();
            $table->foreign('material_option_id')->references('id')->on('material_options')->onDelete('cascade')->onUpdate('cascade');

            $table->string('name')->nullable();
            $table->string('status')->default(1)->comment('0 for inactive, 1 for active');

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
        Schema::dropIfExists('material_types');
    }
};
