<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyEventAttendees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_sets', function (Blueprint $table) {
            $table->foreign('event_attendee_id')
                ->references('id')
                ->on('event_attendees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sets', function (Blueprint $table) {
            $table->dropForeign('event_attendee_id');
        });
    }
}
