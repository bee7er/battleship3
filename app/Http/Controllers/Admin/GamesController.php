<?php

namespace App\Http\Controllers\Admin;

use App\Fleet;
use App\Http\Controllers\Controller;
use App\Game;
use App\Message;
use App\MessageText;
use App\User;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
	 * Show all Games
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function index(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$errors = [];
		$msgs = [];

		$games = Game::getGames();

		//dd($games);

		return view('pages.admin.games.index', compact('loggedIn', 'games', 'errors', 'msgs'));
	}

	/**
	 * Add a new game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addGame(Request $request)
	{
		return $this->editGame($request);
	}

	/**
	 * Edit the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editGame(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$gameId = $request->get('gameId');
		$mode = 'edit';
		if (!$gameId) {
			$mode = 'add';
		}

		$errors = [];
		$msgs = [];

		$game = null;
		try {
			$game = Game::getGame($gameId);
			$users = User::getUsers();
			$statuses = Game::STATUS_ARRAY;

		} catch(Exception $e) {
			Log::notice("Error getting game for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.admin.games.editGame', compact('loggedIn', 'game', 'users', 'statuses', 'mode', 'errors', 'msgs'));
	}

	/**
	 * Update the selected game.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateGame(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$gameId = intval($request->get('gameId'));
		// Game id is null when in add mode
		$mode = (isset($gameId) && $gameId > 0) ? 'edit': 'add';
		try {
			$game = Game::getGame($gameId);
			$game->name = $name = Game::getUniqueGameName($request->get('gameName'), $gameId);
			$game->status = $request->get('status');
			$game->player_one_id = $request->get('playerOneId');
			$game->player_two_id = $request->get('playerTwoId');
			if ('add' == $mode) {
				$game->player_two_link_token = Game::getNewToken();
			}
			$game->save();
			// Check for add mode for the creation of the fleet
			if ('add' == $mode) {
				// Create a fleet from the template set of vessels for the user creating the game
				Fleet::createFleet($game->id, $game->player_one_id);
				$messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_INVITE_OWNER,
					[User::getUser($game->player_one_id)->name,Game::getGame($game->id)->name,User::getUser($game->player_two_id)->name]);
				Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);
				$messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_INVITE,
					[User::getUser($game->player_two_id)->name,Game::getGame($game->id)->name,User::getUser($game->player_one_id)->name]);
				Message::addMessage($messageText, $game->player_one_id, $game->player_two_id);
			}

		} catch(Exception $e) {
			Log::notice("Error updating admin game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/games');
	}

	/**
	 * Delete the selected game
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function deleteGame(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$gameId = intval($request->get('gameId'));
		try {
			$game = Game::getGame($gameId);
			$game->deleteGame();

		} catch(Exception $e) {
			Log::notice("Error deleting game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/games');
	}

	/**
	 * Undelete the selected game
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function undeleteGame(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$gameId = intval($request->get('gameId'));
		try {
			$game = Game::getGame($gameId);
			$game->undeleteGame();

		} catch(Exception $e) {
			Log::notice("Error deleting game: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/games');
	}

}
