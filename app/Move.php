<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Move extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'moves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['game_id', 'player_id', 'row', 'col', 'hit_vessel'];

    /**
     * Retrieve a move
     */
    public static function getMove($id=null)
    {
        $builder = self::select('*');

        $move = $builder
            ->where("moves.id", "=", $id)->get();

        if (isset($move) && count($move) > 0) {
            return $move[0];
        }
        Log::notice("Could not find move for id '$id'");

        return null;
    }

    /**
     * Retrieve all moves for the given game/user
     *
     * @param $gameId
     * @param $userId
     * @return mixed
     */
    public static function getMoves($gameId, $userId=null)
    {
        $builder = self::select([
            'moves.id',
            'moves.game_id',
            'moves.player_id',
            'moves.row',
            'moves.col',
            'moves.hit_vessel',
            'users.id as user_id',
            'users.name as user_name',
        ])
            ->join('users', 'users.id', '=', 'moves.player_id')
            ->orderBy("moves.id");

        $builder = $builder->where("moves.game_id", "=", $gameId);
        // That gets total moves, optionally get moves from a given user
        if (null != $userId) {
            $builder = $builder->where("moves.player_id", "=", $userId);
        }

        $moves = $builder->get();

        return $moves;
    }

    /**
     * Gets the latest move for the specified game.
     *
     * @param $gameId
     * @return mixed
     */
    public static function getLatestMove($gameId)
    {
        $builder = self::select('*')
            ->orderBy("moves.id", "DESC")
            ->limit(1);

        $move = $builder->where("moves.game_id", "=", $gameId)->get();

        if (isset($move) && count($move) > 0) {
            return $move[0];
        }

        return null;
    }
}