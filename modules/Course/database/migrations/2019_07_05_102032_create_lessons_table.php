<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longtext('subtitle')->nullable();
            $table->string('slug')->index();
            $table->longtext('description')->nullable();
            $table->longtext('content');
            $table->integer('sort')->index()->default(0);
            $table->string('type')->index()->nullable();
            $table->unsignedBigInteger('course_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('course_id')
                  ->references('id')->on('courses')
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
        Schema::dropIfExists('lessons');
    }
}
