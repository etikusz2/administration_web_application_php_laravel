<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('login_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->string('firstName', 80);
            $table->string('lastName', 80);
            $table->string('phoneNumber', 20);
            $table->string('idType', 10);
            $table->string('idSerial', 5);
            $table->string('idNumber', 10);
            $table->string('personalIdentificationNumber', 13)->unique();
            $table->string('county', 50);
            $table->string('city', 50);
            $table->string('street', 100);
            $table->string('streetNumber', 10);
            $table->string('block', 10)->nullable();
            $table->string('blockEntrance', 10)->nullable();
            $table->string('apartmentNumber', 10)->nullable();
            $table->string('idImageURL', 150);
            $table->string('selfieImageURL', 150);
            $table->dateTime('createDate');
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_applications');
    }
}

