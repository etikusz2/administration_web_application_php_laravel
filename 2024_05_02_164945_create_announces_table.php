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
        Schema::create('announces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcesCategoryId');
            $table->longText('announceText');
            $table->string('announceImageURL', 150)->nullable();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('updateDate')->nullable();
            $table->dateTime('deleteDate')->nullable();

            $table->foreign('announcesCategoryId')
                ->references('id')->on('announce_categories')
                ->onDelete('cascade');
            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onDelete('set null');


            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announces');
    }
};
