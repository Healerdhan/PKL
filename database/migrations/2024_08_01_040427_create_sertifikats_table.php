<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSertifikatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('siswa_id');
            $table->uuid('dudi_id');
            $table->string('kompetensi_keahlian');
            $table->text('alamat_tempat_pkl');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->uuid('nilai_id');
            $table->string('predikat');
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('dudi_id')->references('id')->on('dudis')->onDelete('cascade');
            $table->foreign('nilai_id')->references('id')->on('nilais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sertifikats');
    }
}
