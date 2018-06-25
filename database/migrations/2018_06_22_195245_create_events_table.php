<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->char('external_id', 50);
            $table->text('name');
            $table->datetime('time');
            $table->float('cost')->default(0.0);
            $table->enum('status', ['settled', 'pending', 'received'])->default('pending');
            $table->float('water')->default(0.0);
            $table->float('ground')->default(0.0);
            $table->text('comment')->nullable();
            $table->index('external_id');
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
        Schema::dropIfExists('events');
    }
}
