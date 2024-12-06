<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginApprovalsTable extends Migration
{
    public function up()
    {
        Schema::create('login_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loginApplicationId');
            $table->unsignedBigInteger('administrativeUnitId');
            $table->tinyInteger('applicationStatus');
            $table->dateTime('createDate');
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('loginApplicationId')->references('id')->on('login_applications')->onDelete('cascade');
            $table->foreign('administrativeUnitId')->references('id')->on('administrative_units')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('login_approvals');
    }
}
