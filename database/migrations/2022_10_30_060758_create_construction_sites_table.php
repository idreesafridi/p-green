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
        Schema::create('construction_sites', function (Blueprint $table) {
            $table->id();
            // $table->UUID('id')->primary();
            $table->string('oldid')->nullable();
            $table->string('name')->nullable();
            $table->string('surename')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('town_of_birth')->nullable();
            $table->string('province')->nullable();
            $table->string('residence_street')->nullable();
            $table->string('residence_house_number')->nullable();
            $table->string('residence_postal_code')->nullable();
            $table->string('residence_common')->nullable();
            $table->string('residence_province')->nullable();
            $table->string('page_status')->default(1);
            $table->string('archive')->default(0)->comment('0/null for extract (active), 1 for archive');
            $table->string('status')->default(1);
            $table->string('pin_location')->nullable();
            // $table->string('latest_status')->default('Preanalisi Fatturare')->nullable();
            $table->string('latest_status')->nullable();
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
        Schema::dropIfExists('construction_sites');
    }
};
