<?php

use App\Game;
use Illuminate\Database\Seeder;
use App\MessageText;
use App\User;
use Illuminate\Support\Facades\DB;

class MessageTextsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('messages')->delete();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_INVITE_OWNER;
        $messageText->text = "Hi %s, a game has been created for you by the system called '%s' against opponent '%s'. *system_admin";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_INVITE;
        $messageText->text = "Hi %s, will you play '%s' with me? %s";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_ACCEPT;
        $messageText->text = "Hi %s, I will love playing '%s' with you. %s";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_READY;
        $messageText->text = "Hi %s and %s, I'm happy to say that '%s' is ready to play. *system_admin";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_WAITING;
        $messageText->text = "Hi %s, %s is waiting for you to finish plotting your fleet in the '%s' game. *system_admin";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_WINNER;
        $messageText->text = "Hi %s, you won the '%s' game.  Well done. %s";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_LOSER;
        $messageText->text = "Hi %s, sadly you lost the '%s' game.  Try again later. %s";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_PLAYER_TWO_ERROR;
        $messageText->text = "Hi %s, sorry you cannot play '%s' against yourself. %s";
        $messageText->type = MessageText::TYPE_SPECIFIC;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

        $messageText = new MessageText();
        $messageText->name = MessageText::MESSAGE_BROADCAST_WELCOME_VERSION_2;
        $messageText->text = "Hi %s, welcome to version two of my battleships game. *system_admin";
        $messageText->type = MessageText::TYPE_BROADCAST;
        $messageText->status = MessageText::STATUS_READY;
        $messageText->save();

    }
}
