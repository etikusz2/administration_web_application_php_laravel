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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serviceId');
            $table->unsignedBigInteger('appointmentUserId');
            $table->date('appointmentDate');
            $table->time('appointmentStartTime');
            $table->time('appointmentEndTime');
            $table->boolean('appointmentStatus')->default(false);

            $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('appointmentUserId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
