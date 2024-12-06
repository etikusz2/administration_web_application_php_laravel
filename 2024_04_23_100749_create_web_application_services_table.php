<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebApplicationServicesTable extends Migration
{
    public function up()
    {
        Schema::create('web_application_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webApplicationId');
            $table->string('webServiceName', 50);

            $table->foreign('webApplicationId')->references('id')->on('web_applications')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('web_application_services');
    }
}

