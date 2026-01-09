<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\MessageText;

class CreateMessageTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_texts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('text');
            $table->enum('type', [
                    MessageText::TYPE_SPECIFIC,
                    MessageText::TYPE_BROADCAST,
                ]
            );
            $table->enum('status', [
                    MessageText::STATUS_READY,
                    MessageText::STATUS_SENT,
                ]
            );
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
        Schema::drop('message_texts');
    }
}