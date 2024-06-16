<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursewaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coursewares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longtext('uri');
            $table->longtext('pathname')->nullable();
            $table->morphs('coursewareable');
            $table->string('type')->index()->default('supliment');
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('CASCADE')
                  ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coursewares');
    }
}
