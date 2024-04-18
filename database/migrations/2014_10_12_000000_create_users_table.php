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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('status')->default(0)->comment('1 for active, 0 for inactive');
            $table->string('birthplace')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('dob')->nullable();
            $table->string('residence_city')->nullable();
            $table->string('residence_province')->nullable();
            $table->string('residence')->nullable();
            $table->string('fiscal_code')->nullable();
            $table->string('professional_college')->nullable();
            $table->string('common_college')->nullable();
            $table->string('registration_number')->nullable();

            $table->string('password');
            $table->string('orignalpass');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
