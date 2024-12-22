<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SiswaDanDudis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dudi_siswa', function (Blueprint $table) {
            $table->uuid('dudi_id');
            $table->uuid('siswa_id');
            $table->timestamps();

            $table->foreign('dudi_id')->references('id')->on('dudis')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');

            $table->primary(['dudi_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
