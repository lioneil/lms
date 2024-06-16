<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonstreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessonstree', function (Blueprint $table) {
            $table->unsignedBigInteger('ancestor_id')->index();
            $table->unsignedBigInteger('descendant_id')->index();
            $table->unsignedBigInteger('depth')->index()->default(0);
            $table->unsignedBigInteger('root')->index()->default(0);
            $table->unique(['ancestor_id', 'descendant_id', 'depth']);
            $table->index(['ancestor_id', 'descendant_id', 'depth']);
            $table->index(['descendant_id', 'depth']);
            $table->index(['depth', 'root']);
            $table->foreign('ancestor_id')
                  ->references('id')
                  ->on('lessons')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('descendant_id')
                  ->references('id')
                  ->on('lessons')
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
        Schema::dropIfExists('lessontree');
    }
}
