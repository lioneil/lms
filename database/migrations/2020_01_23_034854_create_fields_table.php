<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longtext('title');
            $table->string('code')->unique();
            $table->string('type')->default('text');
            $table->longtext('metadata')->nullable();
            $table->unsignedBigInteger('form_id')->index();
            $table->string('group')->nullable()->index();
            $table->timestamps();
            $table->foreign('form_id')
                  ->references('id')->on('forms')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
