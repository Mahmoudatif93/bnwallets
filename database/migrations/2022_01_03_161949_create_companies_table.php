<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
           // $table->string('kind');
           $table->integer('main_company_id')->unsigned();
            $table->string('name');
            $table->integer('enable')->default(0);
            $table->integer('api')->default(0);
            $table->integer('api2')->default(0);
            $table->string('idapi2')->nullable();
            $table->string('AcountID')->nullable();
            $table->string('AcountEmail')->nullable();
            $table->string('AcountPassword')->nullable();
            $table->string('company_image')->default('default.png');
            $table->foreign('main_company_id')->references('id')->on('main_companies')->onDelete('cascade');
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
        Schema::dropIfExists('companies');
    }
}
