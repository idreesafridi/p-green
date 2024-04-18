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
        Schema::create('construction_materials', function (Blueprint $table) {
            // $table->UUID('id')->primary();

            // $table->foreignUuid('construction_site_id')->nullable()->references('id')->on('construction_sites')->onDelete('cascade');

            $table->id();

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('material_list_id')->nullable();
            $table->foreign('material_list_id')->references('id')->on('material_lists')->onDelete('cascade')->onUpdate('cascade');

            $table->string('quantity')->nullable();
            $table->string('state')->nullable();
            $table->string('consegnato')->default(0)->nullable()->comment('1 for true, 0 for false');
            $table->string('avvio')->nullable();
            $table->string('note')->nullable();
            $table->string('montato')->default(0)->nullable()->comment('1 for true, 0 for false');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('delete_status')->default('0');
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
        Schema::dropIfExists('construction_materials');
    }
};
