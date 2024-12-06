<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativeUnitDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('administrative_unit_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('administrativeUnitId');
            $table->string('departmentName', 80);
            $table->unsignedBigInteger('webApplicationId')->nullable();
            $table->string('webApplicationURL', 150)->nullable();

            $table->foreign('administrativeUnitId')->references('id')->on('administrative_units')->onDelete('cascade');
            $table->foreign('webApplicationId')->references('id')->on('web_applications')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('administrative_unit_departments');
    }
}

