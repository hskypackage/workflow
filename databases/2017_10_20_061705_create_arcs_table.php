<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arcs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('workflow_id');
            $table->integer('transition_id');
            $table->integer('from')->comment('指向places表的id');
            $table->integer('to')->comment('指向places表的id');

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
        Schema::dropIfExists('arcs');
    }
}
