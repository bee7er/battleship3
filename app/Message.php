<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Message extends Model
{
    const STATUS_OPEN = 'open';
    const STATUS_READ = 'read';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['message_text', 'status', 'sending_user_id', 'receiving_user_id', 'read_at'];

    /**
     * Find and return the identified message
     */
    public static function getMessage($id=null)
    {
        if (null == $id) {
            // Add mode
            return new Message();
        }

        $message = self::where("messages.id", "=", $id);

        return $message->get()[0];
    }

    /**
     * Retrieve all messages for the given user
     */
    public static function getMessages($receivingUserId)
    {
        $builder = self::select(
            array(
                'messages.id',
                'messages.message_text',
                'messages.status',
                'messages.sending_user_id',
                'messages.receiving_user_id',
                'messages.read_at',
                'sender.name as sender_name',
                'receiver.name as receiver_name',
            )
        )
            ->join('users as sender', 'sender.id', '=', 'messages.sending_user_id')
            ->leftjoin('users as receiver', 'receiver.id', '=', 'messages.receiving_user_id')
            ->orderBy("messages.created_at", "DESC");

        $messages = $builder
            ->where("messages.receiving_user_id", "=", $receivingUserId)
            ->where("messages.status", "=", self::STATUS_OPEN);

        return $messages->get();
    }

    /**
     * Add a new message
     *
     * @param $messageText
     * @param $fromUserId
     * @param $toUserId
     */
    public static function addMessage($messageText, $fromUserId, $toUserId)
    {
        if (!isset($toUserId) || null == $toUserId) {
            // No point adding a message to no one
            return;
        }
        $message = Message::getMessage();
        $message->message_text = $messageText;
        $message->status = self::STATUS_OPEN;
        $message->sending_user_id = $fromUserId;
        $message->receiving_user_id = $toUserId;
        $message->save();
    }

    /**
     * Send a message to all users
     */
    public static function sendAnyBroadcastMessages()
    {
        $messageTexts = MessageText::getBroadcastMessages();
        if (isset($messageTexts) && count($messageTexts) > 0) {
            $users = User::getUsers();
            // Broadcast messages are sent by the System user
            $systemUser = User::systemUser();
            foreach ($messageTexts as $messageText) {
                // Check for system user substitution
                $messageText->text = str_replace(User::SYSTEM_USER_REPLACE,  $systemUser->name, $messageText->text);
                // Send this message to each user
                foreach ($users as $user) {
                    $message = Message::getMessage();
                    // Standard data to embed is the user name
                    $message->message_text = sprintf($messageText->text, $user->name);
                    $message->status = self::STATUS_OPEN;
                    $message->sending_user_id = $systemUser->id;
                    $message->receiving_user_id = $user->id;
                    $message->save();
                }
                // Ok, we are done with this broadcast message
                $messageText->status = MessageText::STATUS_SENT;
                $messageText->save();
            }
        }
    }

}