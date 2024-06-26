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
        Schema::table('construction_missing_columns', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('construction_missing_columns', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Drop the foreign key constraint
            $table->dropColumn('user_id'); // Drop the foreign key column
        });
    }
};
