<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magang', function (Blueprint $table) {
            $table->id();
            $table->string('namalengkap');
            $table->string('nomorinduk'); 
            $table->string('asalinstansi');  
            $table->string('jurusan');  
            $table->string('unitkerja'); 
            $table->string('nomortelepon'); 
            $table->string('email'); 
            $table->string('identitas'); 
            $table->string('suratpermohonanmagang'); 
            $table->string('suratrekomendasimagang'); 
            $table->string('suratpenerimaanmagang'); 
            $table->string('suratselesaimagang'); 
            $table->date('tanggalmulaimagang'); 
            $table->date('tanggalselesaimagang'); 
            $table->string('statusmagang');
            $table->string('nipmentor');
            $table->string('nipmentorpelaksana');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magang');
    }
};
