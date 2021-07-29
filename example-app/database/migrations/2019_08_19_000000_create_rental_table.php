<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventoryId');
            $table->string('name', 256);
            $table->string('adress', 256);
            $table->string('email', 45);
            $table->integer('deposit');
            $table->string('phone', 45);
            $table->date('borrowDate');
            $table->date('dueDate');
            $table->date('returnDate')->nullable();
            $table->string('comment', 256)->nullable();
            $table->unsignedBigInteger('lendingUser');
            $table->unsignedBigInteger('receivingUser')->nullable();

            $table->foreign('inventoryId')->references('id')->on('inventory');
            $table->foreign('lendingUser')->references('id')->on('user');
            $table->foreign('receivingUser')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental');
    }
}
