<?php

namespace App\Http\Controllers;

use App\FleetTemplate;
use App\FleetVessel;
use App\FleetVesselLocation;
use App\Game;
use App\Message;
use App\MessageText;
use App\Move;
use App\User;
use App\Fleet;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class GamesController
 * @package App\Http\Controllers
 */
class GamesController extends Controller
{
	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Show the games created by the current user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function index(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$userId = $this->auth->user()->id;

		$errors = [];
		$msgs = [];

		// Load the games for the current user
		$showDeletedGames = ('1' == $request->get('showDeletedGames', '0'));
		$games = Game::getGames($userId, $showDeletedGames);

		foreach($games as &$game) {
			// Get the user's fleet for each game
			$game->fleet = Fleet::getFleet($game->id, $userId);
			// Check to see if the opponent's fleet exists
			$game->player_two_fleet = Fleet::getFleet($game->id, $game->player_two_id);
		}

		return view('pages.games.games', compact('loggedIn', 'userId', 'games', 'showDeletedGames', 'errors', 'msgs'));
	}

	/**
	 * Add a new game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addGame(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$userId = $this->auth->user()->id;
		$user = User::getUser($userId);

		$errors = [];
		$msgs = [];

		$game = null;
		try {
			$game = Game::getGame();

		} catch(Exception $e) {
			Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.games.editGame', compact('loggedIn', 'game', 'user', 'errors', 'msgs'));
	}

	/**
	 * Edit the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editGame(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$user = $this->auth->user();
		$gameId = $request->get('gameId');

		$errors = [];
		$msgs = [];

		$game = null;
		try {
			$game = Game::getGameDetails($gameId);

		} catch(Exception $e) {
			Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		$users = User::getUsers($user->id);

		return view('pages.games.editGame', compact('loggedIn', 'game', 'user', 'users', 'errors', 'msgs'));
	}

	/**
	 * Update the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateGame(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$user = $this->auth->user();
		$gameId = intval($request->get('gameId'));
		// Game id is null when in add mode
		$mode = (isset($gameId) && $gameId > 0) ? 'edit': 'add';

		$name = $request->get('gameName');
		try {
			$name = Game::getUniqueGameName($name, $gameId);
			$game = Game::getGame($gameId);
			$game->name = $name;
			if ('add' == $mode) {
				$game->player_one_id = $user->id;
				$game->player_two_id = null;
				$game->player_two_link_token = Game::getNewToken();
			}
			$game->save();
			// Check for add mode again for the creation of the fleet
			if ('add' == $mode) {
				// Create a fleet from the template set of vessels for the user creating the game
				Fleet::createFleet($game->id, $user->id);
                $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_INVITE,
                    [User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name,User::getUser($game->player_one_id)->name]);
                Message::addMessage($messageText, $game->player_one_id, $game->player_two_id);
			}

		} catch(Exception $e) {
			Log::notice("Error updating game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/games');
	}

	/**
	 * Edit the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editGrid(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$user = $this->auth->user();
		$userId = $user->id;
		$userToken = $user->user_token;
		$gameId = $request->get('gameId');
		$fleetLocationSize = 0;
		$fleet = null;

		$errors = [];
		$msgs = [];

		$game = null;
		try {
			$game = Game::getGameDetails($gameId);
			if (Game::STATUS_DELETED == $game->status) {
				// This check is required because an opponent player could have a deleted game showing
				return redirect()->intended('/games');
			}

			$fleet = Fleet::getFleetDetails($gameId, $userId);

            $fleetLocationSize = FleetTemplate::getFleetLocationSize();

		} catch(Exception $e) {
			Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.games.editGrid', compact('loggedIn', 'game', 'userId', 'userToken', 'fleet', 'fleetLocationSize', 'errors', 'msgs'));
	}

	/**
	 * Accept the selected game as an opponent.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function acceptGame(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$userId = $this->auth->user()->id;
		$gameId = $request->get('gameId');

		try {
			// Create a fleet from the template set of vessels for the user accepting the challenge
			Fleet::createFleet($gameId, $userId);

			// Message player 1 that the game is accepted
			$game = Game::getGame($gameId);
            $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_ACCEPT,
                [
                    User::getUser($game->player_one_id)->name,
                    Game::getGame($game->id)->name,
                    User::getUser($game->player_two_id)->name,
                ]
            );
			Message::addMessage($messageText, $game->player_two_id, $game->player_one_id);

		} catch(Exception $e) {
			Log::notice("Error accepting game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended("/editGrid?gameId=$gameId");
	}

	/**
	 * Play the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function playGrid(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$myUser = $this->auth->user();
		$gameId = $request->get('gameId');
		$fleetId = 0;
		$fleet = null;

		$errors = [];
		$msgs = [];

		$game = $theirUser = null;
		$currentUserIsPlayerOne = false;
		try {
			$game = Game::getGameDetails($gameId);
			if (Game::STATUS_DELETED == $game->status) {
				// This check is required because an opponent player could have a deleted game showing
				return redirect()->intended('/games');
			}

            // Find the latest move for the current game
			$latestMove = Move::getLatestMove($gameId);

			// We must be careful to distinguish between the game owner and the opponent, because
			// when we get to the play grid it can be either.  We will call it 'myFleet' and 'theirFleet'
			$myFleet = Fleet::getFleetDetails($gameId, $myUser->id);

			$currentUserIsPlayerOne = ($myUser->id == $game->player_one_id) ? true: false;
			if ($currentUserIsPlayerOne) {
				$theirUser = User::getUser($game->player_two_id);
				$theirFleet = Fleet::getFleetDetails($gameId, $game->player_two_id);
			} else {
				$theirUser = User::getUser($game->player_one_id);
				$theirFleet = Fleet::getFleetDetails($gameId, $game->player_one_id);
			}
			$myGo = false;
			if ($latestMove == null) {
				if ($currentUserIsPlayerOne) {
					// No previous moves, so player 1 (game owner) starts things off
					$myGo = true;
				}
			} elseif ($latestMove->player_id == $myUser->id && 1 == $latestMove->hit_vessel) {
				// I had a successful hit with the last move and continue my go
				$myGo = true;
			} elseif ($latestMove->player_id != $myUser->id && 0 == $latestMove->hit_vessel) {
				// The opponent had the last move, and did not have a successful hit
				$myGo = true;
			}

            $myMoves = Move::getMoves($gameId, $myUser->id);
            $theirMoves = Move::getMoves($gameId, $theirUser->id);

            //dd($theirFleet);

		} catch(Exception $e) {
			Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.games.playGrid', compact('loggedIn', 'game', 'currentUserIsPlayerOne', 'myFleet', 'theirFleet', 'myUser', 'theirUser', 'myGo', 'myMoves', 'theirMoves', 'errors', 'msgs'));
	}

    /**
     * Replay a completed game.
     *
     * @param Request $request
     * @return Response
     */
    public function replay(Request $request)
    {
        if (!$this->auth->check()) {
            return redirect()->intended('error');
        }

        $loggedIn = true;
        $myUser = $this->auth->user();
        $gameId = $request->get('gameId');

        $errors = [];
        $msgs = [];

        $game = $theirUser = null;
        $myFleet = $theirFleet = null;
        $currentUserIsPlayerOne = $myGo = false;
        $allMoves = null;
        try {
            $game = Game::getGameDetails($gameId);

            $gameError = false;
            if (Game::STATUS_DELETED == $game->status) {
                $errors[] = "The selected game is deleted";
                $gameError = true;
            } elseif (Game::STATUS_COMPLETED != $game->status) {
                $errors[] = "The selected game has not yet completed playing";
                $gameError = true;
            }

            // We must be careful to distinguish between the game owner and the opponent, because
            // when we get to the play grid it can be either.  We will call it 'myFleet' and 'theirFleet'
            $myFleet = Fleet::getFleetDetails($gameId, $myUser->id);

            $currentUserIsPlayerOne = ($myUser->id == $game->player_one_id) ? true : false;
            if ($currentUserIsPlayerOne) {
                $theirUser = User::getUser($game->player_two_id);
                $theirFleet = Fleet::getFleetDetails($gameId, $game->player_two_id);
            } else {
                $theirUser = User::getUser($game->player_one_id);
                $theirFleet = Fleet::getFleetDetails($gameId, $game->player_one_id);
            }
            // For replay purposes we retrieve all moves and use them to simulate the original game
            $allMoves = Move::getMoves($gameId);
            // Check the first move, me or them
            $myGo = ($allMoves[0]->player_id == $myUser->id ? 'true': 'false');

        } catch(Exception $e) {
            Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
            $errors[] = $e->getMessage();
        }

        return view('pages.games.replay', compact('loggedIn', 'game', 'currentUserIsPlayerOne', 'myFleet', 'theirFleet', 'myUser', 'theirUser', 'allMoves', 'myGo', 'gameError', 'errors', 'msgs'));
    }

	/**
	 * Soft deletes the game
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function deleteGame(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		try {
			$game = Game::getGame($request->get('gameId'));
			$game->deleteGame();

		} catch(\Exception $e) {
			Log::notice("Error deleting game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/games');
	}
}
