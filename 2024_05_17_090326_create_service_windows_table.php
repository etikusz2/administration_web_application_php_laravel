<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceWindowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_windows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('administrativeUnitId');
            $table->string('windowName', 150)->nullable();
            $table->string('windowLocation', 150)->nullable();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('administrativeUnitId')
                ->references('id')
                ->on('administrative_units')
                ->onDelete('cascade');

            $table->index('administrativeUnitId', 'fk_service_windows_administrativeUnitId_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_windows');
    }
}
