<?php

namespace App\Http\Controllers;

use App\Game;
use App\Message;
use App\User;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class LeaderboardController
 * @package App\Http\Controllers
 */
class LeaderboardController extends Controller
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
	 * Show the leader board for all users
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
		$user = $this->auth->user();
		$users = User::getLeaderboardUsers();

		$errors = [];
		$msgs = [];

		return view('pages.leaderboard', compact('loggedIn', 'user', 'users', 'errors', 'msgs'));
	}
}
