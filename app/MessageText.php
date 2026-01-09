<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MessageText extends Model
{
    const TYPE_SPECIFIC = 'specific';
    const TYPE_BROADCAST = 'broadcast';

    const STATUS_READY = 'ready';
    const STATUS_SENT = 'sent';
    const STATUS_ARRAY = [self::STATUS_READY, self::STATUS_SENT];

    const MESSAGE_INVITE = "Invite player";
    const MESSAGE_INVITE_OWNER = "Invite owner";
    const MESSAGE_ACCEPT = "Accept invitation";
    const MESSAGE_READY = "Game ready";
    const MESSAGE_WAITING = "Waiting";
    const MESSAGE_WINNER = "Winner";
    const MESSAGE_LOSER = "Loser";
    const MESSAGE_PLAYER_TWO_ERROR = "Player Two Error";

    const MESSAGE_BROADCAST_WELCOME_VERSION_2 = "Welcome to Version 2";

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'message_texts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'text', 'type', 'status'];

    /**
     * Find and return the identified message text
     */
    public static function getMessageText($id=null)
    {
        if (null == $id) {
            // Add mode
            return new MessageText();
        }

        $message = self::where("message_texts.id", "=", $id);

        return $message->get()[0];
    }

    /**
     * Find and return the identified message text
     */
    private static function getMessageTextByName($name)
    {
        $message = self::where("message_texts.name", "=", $name);

        return $message->get()[0];
    }

    /**
     * Retrieve message text given the appropriate parameters
     *
     * @param $messageName
     * @param $msgDataAry
     * @return string
     */
    public static function retrieveMessageText($messageName, $msgDataAry)
    {
        $systemUser = User::systemUser();

        $messageText = self::getMessageTextByName($messageName);
        $messageText->text = str_replace(User::SYSTEM_USER_REPLACE,  $systemUser->name, $messageText->text);

        return sprintf($messageText->text, $msgDataAry[0], $msgDataAry[1], $msgDataAry[2]);
    }

    /**
     * Returns any broadcast messages, which, by default, have not yet been processed
     */
    public static function getBroadcastMessages($status=self::STATUS_READY)
    {
        $builder = self::select('*')
            ->where("message_texts.type", "=", self::TYPE_BROADCAST)
            ->orderBy("message_texts.name");

        if (null != $status) {
            $builder->where("message_texts.status", "=", $status);
        }

        return $builder->get();
    }

}