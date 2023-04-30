<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->string("d_codigo",10);
            $table->string("d_asenta",120);
            $table->string("d_tipo_asenta",120);
            $table->string("D_mnpio",120);
            $table->string("d_estado",120);
            $table->string("d_ciudad",120)->nullable();
            $table->string("d_CP",120)->nullable();
            $table->string("c_estado",120)->nullable();
            $table->string("c_oficina",120);
            $table->string("c_CP",10)->nullable();
            $table->string("c_tipo_asenta",10)->nullable();
            $table->string("c_mnpio",10)->nullable();
            $table->string("id_asenta_cpcons",10)->nullable();
            $table->string("d_zona",120);
            $table->string("c_cve_ciudad",120)->nullable();
            $table->index('d_codigo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zip_codes');
    }
}
