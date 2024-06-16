<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublishableFieldsToCoursewaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coursewares', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->timestamp('drafted_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coursewares', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'drafted_at', 'expired_at']);
        });
    }
}
