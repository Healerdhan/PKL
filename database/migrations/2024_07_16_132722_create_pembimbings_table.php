<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembimbingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembimbings', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('nama_pegawai');
            $table->uuid('dudi_id1')->nullable();
            $table->uuid('dudi_id2')->nullable();
            $table->uuid('dudi_id3')->nullable();
            $table->uuid('dudi_id4')->nullable();
            $table->uuid('dudi_id5')->nullable();
            $table->timestamps();

            $table->foreign('dudi_id1')->references('id')->on('dudis')->onDelete('set null');
            $table->foreign('dudi_id2')->references('id')->on('dudis')->onDelete('set null');
            $table->foreign('dudi_id3')->references('id')->on('dudis')->onDelete('set null');
            $table->foreign('dudi_id4')->references('id')->on('dudis')->onDelete('set null');
            $table->foreign('dudi_id5')->references('id')->on('dudis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembimbings');
    }
}
