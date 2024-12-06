<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceWindowServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_window_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serviceWindowId');
            $table->unsignedBigInteger('serviceId');
            $table->date('availableUntil')->nullable();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('serviceWindowId')
                ->references('id')
                ->on('service_windows')
                ->onDelete('cascade');

            $table->foreign('serviceId')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');

            $table->index('serviceWindowId', 'fk_service_window_services_serviceWindowId_idx');
            $table->index('serviceId', 'fk_service_window_services_serviceId_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_window_services');
    }
}

