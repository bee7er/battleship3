<?php

namespace App;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Game extends Model
{
    const STATUS_EDIT = 'edit';
    const STATUS_WAITING = 'waiting';
    const STATUS_READY = 'ready';
    const STATUS_ENGAGED = 'engaged';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELETED = 'deleted';
    const STATUS_UNDELETED = 'undeleted';

    const STATUS_ARRAY = [self::STATUS_EDIT, self::STATUS_WAITING, self::STATUS_READY, self::STATUS_ENGAGED, self::STATUS_COMPLETED, self::STATUS_DELETED, self::STATUS_UNDELETED];
    const STATUS_ACTIVE_ARRAY = [self::STATUS_ENGAGED, self::STATUS_COMPLETED, self::STATUS_DELETED, self::STATUS_UNDELETED];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'player_one_id', 'player_two_id', 'player_two_link_token', 'winner_id', 'started_at', 'ended_at', 'deleted_at'
    ];

    /**
     * Retrieve a game
     */
    public static function getGame($id=null)
    {
        if (null == $id) {
            // Add mode
            return new Game();
        }

        $builder = self::select(
            array(
                'games.id',
                'games.name',
                'games.status',
                'games.player_one_id',
                'games.player_two_id',
                'games.player_two_link_token',
                'games.winner_id',
                'games.started_at',
                'games.ended_at',
                'games.deleted_at'
            )
        );

        $game = $builder
            ->where("games.id", "=", $id)->get();

        if (isset($game) && count($game) > 0) {
            return $game[0];
        }
        Log::notice("Could not find game for id '$id'");

        return null;
    }

    /**
     * Retrieve a game by name
     */
    public static function getGameByName($name)
    {
        return self::select('*')->where("games.name", "=", $name)->get();
    }

    /**
     * Retrieve a unique game name
     */
    public static function getUniqueGameName($name, $gameId)
    {
        $game = self::getGameByName($name);
        // Make it unique
        if (isset($game) && count($game) > 0) {
            if ($game[0]->id == $gameId) {
                // It is unique to the same game
                return $name;
            }
            // Add numbers until we get a unique name
            for ($n=1; $n<25; $n++) {
                $name = $name."_$n";
                $game = self::getGameByName($name);
                if (!isset($game) || count($game) <= 0) {
                    return $name;
                }
            }
            throw new Exception("Could not generate a unique name and gave up.");
        }
        // It is unique
        return $name;
    }

    /**
     * Retrieve all games for the given user, where they created the game and where they
     * have been nominated as player 2
     *
     * @param $userId
     * @param $showDeletedGames - check whether they have been soft deleted
     * @return mixed
     */
    public static function getGames($userId=null, $showDeletedGames=true)
    {
        $builder = self::select(
            array(
                'games.id',
                'games.name',
                'games.player_one_id',
                'player_one.name as player_one_name',
                'games.player_two_id',
                'games.player_two_link_token',
                'player_two.name as player_two_name',
                'games.winner_id',
                'games.status',
                'games.started_at',
                'games.ended_at',
                'games.deleted_at',
            )
        )
            ->leftjoin('users as player_two', 'player_two.id', '=', 'games.player_two_id')
            ->leftjoin('users as player_one', 'player_one.id', '=', 'games.player_one_id')
            ->orderBy("games.name");

        if (false == $showDeletedGames) {
            $builder = $builder
                ->where("games.status", "!=", Game::STATUS_DELETED);
        }

        if (null != $userId) {
            // Sub-function used to group the player tests, otherwise the logic
            // with deleted does not work due to association/precedence of operators
            $builder = $builder
                ->where(function($query) use ($userId)
                {
                    $query->where("games.player_one_id", "=", $userId)
                        ->orWhere("games.player_two_id", "=", $userId);
                });
        }

        //dd($builder->toSql());

        $games = $builder->get();

        return $games;
    }

    /**
     * Retrieve a game and its joined entities
     */
    public static function getGameDetails($id=null)
    {
        $builder = self::select(
            array(
                'games.id',
                'games.name as game_name',
                'games.player_one_id',
                'users1.name as player_one_name',
                'games.player_two_id',
                'games.player_two_link_token',
                'users2.name as player_two_name',
                'games.winner_id',
                'games.status',
                'games.started_at',
                'games.ended_at',
                'games.deleted_at',
            )
        )
            ->join('users as users1', 'users1.id', '=', 'games.player_one_id')
            ->leftjoin('users as users2', 'users2.id', '=', 'games.player_two_id')
            ->orderBy("games.name");

        $gameCollection = $builder
            ->where("games.id", "=", $id);

        if (!isset($gameCollection) || $gameCollection->count() <= 0) {
            throw new Exception("Could not find game with id '$id'");
        }
        if ($gameCollection->count() > 1) {
            throw new Exception("More than one game found with id '$id'");
        }

        // Get the game object and add the player two link
        $game = $gameCollection->get()[0];
        if (null == $game->player_two_id) {
            $game->player_two_link = env("APP_URL", "/") . "playerTwo?gameToken={$game->player_two_link_token}";
        }

        return $game;
    }

    /**
     * Check the satatus of the game
     */
    public static function setGameStatus($gameId)
    {
        $game = self::getGame($gameId);
        if (self::STATUS_COMPLETED == $game->status) {
            // The game is already completed, just exit
            return $game->status;
        }

        $gameStatus = self::STATUS_EDIT;
        // If there are any moves, then it has started
        $moves = Move::getMoves($gameId);
        if (isset($moves) && count($moves) > 0) {
            if (1 == count($moves)) {
                // First move, set the game started datetime
                $game->started_at = date('Y-m-d H:i:s');
            }
            $gameStatus = self::STATUS_ENGAGED;

            $playerOneFleet = Fleet::getFleet($gameId, $game->player_one_id);
            // Check the fleet vessel locations to see if all parts of all vessels have been destroyed
            $isFleetDestroyed = FleetVessel::isFleetDestroyed($playerOneFleet->id);
            if ($isFleetDestroyed) {
                $gameStatus = self::STATUS_COMPLETED;
                $game->winner_id = $game->player_two_id;
                // Notify both parties
                $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_LOSER,
                    [User::getUser($game->player_one_id)->name,Game::getGame($game->id)->name,User::systemUser()->name]);
                Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);

                $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_WINNER,
                    [User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name,User::systemUser()->name]);
                Message::addMessage($messageText, User::systemUser()->id, $game->player_two_id);
            } else {
                // Ok, still fighting, check the opponent's fleet
                $playerTwoFleet = Fleet::getFleet($gameId, $game->player_two_id);
                $isFleetDestroyed = FleetVessel::isFleetDestroyed($playerTwoFleet->id);
                if ($isFleetDestroyed) {
                    $gameStatus = self::STATUS_COMPLETED;
                    $game->winner_id = $game->player_one_id;
                    // Notify both parties
                    $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_LOSER,
                        [User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name,User::systemUser()->name]);
                    Message::addMessage($messageText, User::systemUser()->id, $game->player_two_id);

                    $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_WINNER,
                        [User::getUser($game->player_one_id)->name,Game::getGame($game->id)->name,User::systemUser()->name]);
                    Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);
                }
            }
            if ($gameStatus == self::STATUS_COMPLETED) {
                // Last move, set the game ended datetime
                $game->ended_at = date('Y-m-d H:i:s');
            }
        } else {
            $playerOneReady = Fleet::isFleetReady($gameId, $game->player_one_id);
            $playerTwoReady = Fleet::isFleetReady($gameId, $game->player_two_id);
            if ($playerOneReady && $playerTwoReady) {
                $gameStatus = self::STATUS_READY;
                // Message the player 1 and player 2 that the game is ready
                $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_READY,
                    [User::getUser($game->player_one_id)->name,User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name]);
                Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);

                $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_READY,
                    [User::getUser($game->player_two_id)->name,User::getUser($game->player_one_id)->name,Game::getGame($game->id)->name]);
                Message::addMessage($messageText, User::systemUser()->id, $game->player_two_id);

            } elseif ($playerOneReady || $playerTwoReady) {
                $gameStatus = self::STATUS_WAITING;
                // If the player 1 or player 2 is not yet started then send them a message
                if ($playerOneReady && Fleet::isFleetNotStarted($game->id, $game->player_two_id)) {
                    $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_WAITING,
                        [User::getUser($game->player_two_id)->name,User::getUser($game->player_one_id)->name,Game::getGame($game->id)->name]);
                    Message::addMessage($messageText, User::systemUser()->id, $game->player_two_id);

                } elseif ($playerTwoReady && Fleet::isFleetNotStarted($game->id, $game->player_one_id)) {
                    $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_WAITING,
                        [User::getUser($game->player_one_id)->name,User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name]);
                    Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);
                }
            }
        }

        $game->status = $gameStatus;
        $game->save();
    }

    /**
     * Delete a game
     */
    public function deleteGame()
    {
        $this->status = self::STATUS_DELETED;
        $this->deleted_at = date("Y-m-d H:i:s");
        $this->save();
    }

    /**
     * Undelete a game
     */
    public function undeleteGame()
    {
        $this->status = self::STATUS_UNDELETED;
        $this->deleted_at = null;
        $this->save();
    }

    /**
     * Get a count of the number of wins by the specified user
     */
    public static function getWinnerCount($userId)
    {
        $wins = self::select('*')
            ->where("games.winner_id", "=", $userId)->get();

        if (isset($wins) && count($wins) > 0) {
            return count($wins);
        }

        return 0;
    }

    /**
     * Retrieve a game by player two link token
     * @param $playerTwoLinkToken
     * @return mixed
     */
    public static function getGameByPlayerTwoLinkToken($playerTwoLinkToken)
    {
        return self::select('*')->where("games.player_two_link_token", "=", $playerTwoLinkToken)->first();
    }

    /**
     * Set player two details
     * @param $playerTwoLinkToken
     * @return mixed
     */
    public function setPlayerTwoForGame($userId)
    {
        // Update the game to show player two
        $this->player_two_id = $userId;
        $this->save();
        // Clear the player two session variable
        setSessionVariable(Controller::SESSION_VAR_GAME_TOKEN, null);
    }

    /**
     * Get a new, unique player_two_link_token.
     */
    public static function getNewToken()
    {
        $token = null;
        // Try to get a unique token
        for ($i=0; $i<10; $i++) {
            $token = Str::random(16);
            // Check the token is unique
            $game = Game::where('player_two_link_token', $token)->first();
            if (!$game) {
                return $token;  // Ok, is unique
            }
        }
        throw new \Exception("Could not generate a unique token for new game");
    }
}