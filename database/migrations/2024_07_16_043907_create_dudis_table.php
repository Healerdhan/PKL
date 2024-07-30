<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDudisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dudis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tempat');
            $table->integer('jumlah');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->uuid('siswa_id1')->nullable();
            $table->uuid('siswa_id2')->nullable();
            $table->uuid('siswa_id3')->nullable();
            $table->uuid('siswa_id4')->nullable();
            $table->uuid('siswa_id5')->nullable();
            $table->uuid('siswa_id6')->nullable();
            $table->uuid('siswa_id7')->nullable();
            $table->uuid('siswa_id8')->nullable();
            $table->uuid('siswa_id9')->nullable();
            $table->uuid('siswa_id10')->nullable();
            $table->uuid('siswa_id11')->nullable();
            $table->uuid('siswa_id12')->nullable();
            $table->uuid('siswa_id13')->nullable();
            $table->uuid('siswa_id14')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id1')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id2')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id3')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id4')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id5')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id6')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id7')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id8')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id9')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id10')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id11')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id12')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id13')->references('id')->on('siswas')->onDelete('set null');
            $table->foreign('siswa_id14')->references('id')->on('siswas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dudis');
    }
}
