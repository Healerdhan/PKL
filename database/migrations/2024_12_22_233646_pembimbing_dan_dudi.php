<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PembimbingDanDudi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembimbing_dudi', function (Blueprint $table) {
            $table->uuid('pembimbing_id');
            $table->uuid('dudi_id');
            $table->timestamps();

            $table->foreign('pembimbing_id')->references('id')->on('pembimbings')->onDelete('cascade');
            $table->foreign('dudi_id')->references('id')->on('dudis')->onDelete('cascade');

            $table->primary(['pembimbing_id', 'dudi_id']);
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
