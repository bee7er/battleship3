<?php

namespace App\Http\Controllers;

use App\Game;
use App\MessageText;
use App\User;
use App\Message;
use App\Vessel;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use SinglePlayerHandler;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
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
	 * Show the application dashboard to the user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function index(Request $request)
	{
		$allMoves = \App\Move::getAllMovesBySystemUser( 2 );

		//dd($allMoves);
		//=======================================
		$singlePlayerHandler = new SinglePlayerHandler();
		$singlePlayerHandler->setAllMoves($allMoves);
		// Analyse all moves by the System and derive the next cell to hit
		$singlePlayerHandler->processSinglePlayerMoves();

		//========================================
		$loggedIn = false;
		$userToken = '';
		$errors = [];
		$msgs = [];

		if ($this->auth->check()) {
			$loggedIn = true;
			$user = $this->auth->user();
			$userToken = $user->user_token;
			// Check if there are any system messages to be broadcast
			// With just a small number of users this technique is ok.  If lots more come on board then this
			// function should go into a kron job
			Message::sendAnyBroadcastMessages();

			$msgs = Message::getMessages($user->id)->toArray();
		}

		return view('pages.home', compact('loggedIn', 'userToken', 'errors', 'msgs'));
	}

	/**
	 * Player 2 responding to an invitation to a game
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function playerTwo(Request $request)
	{
		$loggedIn = false;
		$user = null;
		if ($this->auth->check()) {
			$loggedIn = true;
			$user = $this->auth->user();
		}

		$errors = [];
		$msgs = [];

		$gameToken = $request->get('gameToken');
		$game = Game::getGameByPlayerTwoLinkToken($gameToken);
		if (isset($game) && null != $game) {
			if (in_array($game->status, Game::STATUS_ACTIVE_ARRAY)) {
				$errors[] = 'Sorry, that game can no longer be edited. Login and add your own game.';
			} else {
				// Game is available for accepting and or editing
				if (null != $game->player_two_id) {
					// Game has been accepted by someone
					if ($loggedIn) {
						if ($game->player_two_id == $user->id) {
							// A user is logged in, is it the same player 2?
							return redirect()->intended('/editGrid?gameId=' . $game->id);

						} else {
							$errors[] = 'Sorry, that game has already been claimed by someone else.';
							return view('pages.errors', compact('loggedIn', 'errors', 'msgs'));
						}
					} else {
						// Go to player 2 page for the current user to login
						setSessionVariable(self::SESSION_VAR_GAME_TOKEN, $game);
					}
				} else {
					// No one has accepted the game yet
					if ($loggedIn) {
						// A user is logged in, is game owner the same user?
						if ($game->player_one_id == $user->id) {
							$errors[] = MessageText::retrieveMessageText(MessageText::MESSAGE_PLAYER_TWO_ERROR,
								[User::getUser($game->player_one_id)->name, $game->name, User::systemUser()->name]);
							return view('pages.errors', compact('loggedIn', 'errors', 'msgs'));

						}
						// The currently logged in user accepts the game
						$game->setPlayerTwoForGame($user->id);

						return redirect()->intended('/acceptGame?gameId=' . $game->id);
					}
					// Not logged in, so drop through to the player 2 page
					setSessionVariable(self::SESSION_VAR_GAME_TOKEN, $game);
				}
			}
		} else {
			$errors[] = "Sorry, game could not be found for gameToken '$gameToken'.";
			return view('pages.errors', compact('loggedIn', 'errors', 'msgs'));
		}

		return view('pages.playerTwo', compact('loggedIn', 'errors', 'msgs'));
	}


	/**
	 * Show the about page to the user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function about(Request $request)
	{
		$loggedIn = false;
		if ($this->auth->check()) {
			$loggedIn = true;
		}

		$errors = [];
		$msgs = [];

		$vessels = Vessel::getVessels();

		return view('pages.about', compact('loggedIn', 'vessels', 'errors', 'msgs'));
	}

	/**
	 * Show the profile page to the user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function profile(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$loggedInUser = $this->auth->user();
		$userId = $request->get('userId');
		if (null != $userId) {
			$user = User::getUser($userId);
		} else {
			$user = $this->auth->user();
		}

		$user->wins = Game::getWinnerCount($user->id);
		$from = $request->get('from');

		$errors = [];
		$msgs = [];

		return view('pages.profile', compact('loggedIn', 'loggedInUser', 'user', 'from', 'errors', 'msgs'));
	}

	/**
	 * Update the user profile.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateProfile(Request $request)
	{
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$userId = intval($request->get('userId'));
		try {
			$user = User::getUser($userId);
			$user->password_hint = $request->get('passwordHint');

			if ("" != $request->get('password')) {
				$user->password = Hash::make($request->get('password'));
			}
			if (!isset($user->user_token) || '' == $user->user_token) {
				$user->user_token = User::getNewToken();
			}
			$user->save();

		} catch(Exception $e) {
			Log::notice("Error updating user profile: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		$from = $request->get('from');
		$redirectTo = '/home';
		if ('lb' == $from) {
			$redirectTo = '/leaderboard';
		}

		return redirect()->intended($redirectTo);
	}

	/**
	 * Show the error page to the user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function error(Request $request)
	{
		$loggedIn = false;
		if ($this->auth->check()) {
			$loggedIn = true;
		}

		$errors = [];
		$msgs = [];

		return view('pages.error', compact('loggedIn', 'errors', 'msgs'));
	}

}
