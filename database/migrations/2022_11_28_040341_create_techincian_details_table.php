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
        Schema::create('techincian_details', function (Blueprint $table) {
            // $table->UUID('id')->primary();
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('professional_title')->comment('user professional title (they are engineers with specific titles example');
            $table->string('professional_id')->comment('user professional ID (for lawyers)');

            $table->string('city')->comment('users city');
            $table->string('current_province')->comment('users current province, user province where they received their title ');
            $table->string('birth_province')->comment('users birth province');
            $table->string('ccn')->comment('user citizen card number');
            $table->string('address')->comment('user residency address');

            $table->string('status')->default('0');
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
        Schema::dropIfExists('techincian_details');
    }
};
