<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
	        $table->string('unique', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('source', 255)->nullable();
            $table->string('alt', 255)->nullable();
            $table->string('mime', 20)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
