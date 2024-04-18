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
        Schema::create('document_and_contacts', function (Blueprint $table) {
            $table->id();

            // $table->foreignUuid('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade');

            $table->unsignedBigInteger('construction_site_id')->nullable();
            $table->foreign('construction_site_id')->references('id')->on('construction_sites')->onDelete('cascade')->onUpdate('cascade');

            $table->string('document_number')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('release_date')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('fiscal_document_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('alt_refrence_name')->nullable();
            $table->string('alt_contact_number')->nullable();

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
        Schema::dropIfExists('document__and__contacts');
    }
};
