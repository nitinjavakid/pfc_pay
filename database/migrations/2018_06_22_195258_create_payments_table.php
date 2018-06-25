<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->enum('type', ['cash', 'paytm', 'instamojo']);
            $table->enum('status', ['pending', 'paid', 'refunded']);
            $table->char('external_id', 50)->nullable();
            $table->foreign('event_id')
               ->references('id')
               ->on('events')
               ->onDelete('cascade');
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
        Schema::dropIfExists('payments');
    }
}
