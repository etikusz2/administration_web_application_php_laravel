<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceWindowSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_window_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serviceWindowId');
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('serviceWindowId')
                ->references('id')
                ->on('service_windows')
                ->onDelete('cascade');

            $table->index('serviceWindowId', 'fk_service_window_schedules_serviceWindowId_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_window_schedules');
    }
}
