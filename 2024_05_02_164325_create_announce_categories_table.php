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
        Schema::create('announce_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('administrativeUnitId');
            $table->string('announceCategoryName', 50);
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('administrativeUnitId')
                ->references('id')->on('administrative_units')
                ->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announce_categories');
    }
};
