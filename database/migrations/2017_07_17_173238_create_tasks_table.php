<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('name');
            $table->text('description');
            $table->dateTime('deadline');
            $table->boolean('completed')->default(0);
            $table->boolean('private')->default(0);
            $table->boolean('warned')->default(0);
            $table->timestamps();
        });

        Schema::create('followers', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('user_id');
            $table->integer('task_id');
            $table->primary(['user_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('followers');
    }
}
