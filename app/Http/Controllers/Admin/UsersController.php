<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends Controller
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
	 * Show all users
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

		$users = User::getUsers();

		return view('pages.admin.users.index', compact('loggedIn', 'users', 'errors', 'msgs'));
	}

	/**
	 * Add a new user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addUser(Request $request)
	{
		return $this->editUser($request);
	}

	/**
	 * Edit the selected user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editUser(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$userId = $request->get('userId');
		$mode = 'edit';
		if (!$userId) {
			$mode = 'add';
		}

		$errors = [];
		$msgs = [];

		$user = null;
		try {
			$user = User::getUser($userId);

		} catch(Exception $e) {
			Log::notice("Error getting user for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.admin.users.editUser', compact('loggedIn', 'user', 'mode', 'errors', 'msgs'));
	}

	/**
	 * Update the selected user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateUser(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$userId = intval($request->get('userId'));
		// User id is null when in add mode
		$mode = (isset($userId) && $userId > 0) ? 'edit': 'add';
		try {
			$user = User::getUser($userId);
			$user->name = $request->get('userName');
			$user->password_hint = $request->get('passwordHint');
			$user->games_played = $request->get('games_played');
			$user->vessels_destroyed = $request->get('vessels_destroyed');
			$user->points_scored = $request->get('points_scored');

			if ("" != $request->get('password')) {
				$user->password = Hash::make($request->get('password'));
			}
			if ('add' == $mode || (!isset($user->user_token) || '' == $user->user_token)) {
				$user->user_token = User::getNewToken();
			}
			$user->save();

		} catch(Exception $e) {
			Log::notice("Error updating user: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/users');
	}

}
