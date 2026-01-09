<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
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
	 * Show the admin dashboard to the user.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function index(Request $request)
	{
		$loggedIn = false;
		if (!$this->auth->check()) {
			return redirect()->intended('error');
		}

		$user = $this->auth->user();
		if (!$user->admin) {
			return redirect()->intended('error');
		}

		$loggedIn = true;

		$errors = [];
		$msgs = [];

		return view('pages.admin.admin', compact('loggedIn', 'errors', 'msgs'));
	}

}
