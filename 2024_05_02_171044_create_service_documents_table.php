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
        Schema::create('service_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serviceId');
            $table->string('documentName', 50);
            $table->string('documentDescription', 150);
            $table->string('documentURL', 150);
            $table->unsignedBigInteger('requestId')->nullable();
            $table->unsignedBigInteger('createdForUserId')->nullable();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('requestId')->references('id')->on('service_requests')->onDelete('set null');
            $table->foreign('createdForUserId')->references('id')->on('users')->onDelete('set null');
            $table->foreign('createdByUserId')->references('id')->on('users')->onDelete('set null');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_documents');
    }
};
