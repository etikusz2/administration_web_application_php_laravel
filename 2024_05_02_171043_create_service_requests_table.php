<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requestByUserId');
            $table->unsignedBigInteger('serviceId')->nullable();
            $table->date('requestDate')->nullable();
            $table->tinyInteger('requestStatus')->nullable();
            $table->string('requestComments', 100)->nullable();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('requestByUserId')->references('id')->on('users');
            $table->foreign('serviceId')->references('id')->on('services');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
}
