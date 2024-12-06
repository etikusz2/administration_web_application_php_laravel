<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departmentId');
            $table->string('serviceName', 50);
            $table->text('serviceDescription');
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('departmentId')
                ->references('id')->on('administrative_unit_departments')
                ->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
