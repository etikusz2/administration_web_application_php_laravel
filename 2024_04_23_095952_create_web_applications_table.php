<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('web_applications', function (Blueprint $table) {
            $table->id();
            $table->string('webApplicationName', 50);
            $table->tinyInteger('webApplicationType');
        });
    }

    public function down()
    {
        Schema::dropIfExists('web_applications');
    }
}

