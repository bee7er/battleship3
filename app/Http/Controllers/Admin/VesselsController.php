<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Vessel;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class VesselsController
 * @package App\Http\Controllers
 */
class VesselsController extends Controller
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
	 * Show all Vessels
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

		$vessels = Vessel::getVessels();

		return view('pages.admin.vessels.index', compact('loggedIn', 'vessels', 'errors', 'msgs'));
	}

	/**
	 * Add a new vessel.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addVessel(Request $request)
	{
		return $this->editVessel($request);
	}

	/**
	 * Edit the selected vessel.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editVessel(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$vesselId = $request->get('vesselId');
		$mode = 'edit';
		if (!$vesselId) {
			$mode = 'add';
		}

		$errors = [];
		$msgs = [];

		$vessel = null;
		try {
			$vessel = Vessel::getVessel($vesselId);
		} catch(Exception $e) {
			Log::notice("Error getting vessel for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.admin.vessels.editVessel', compact('loggedIn', 'vessel', 'mode', 'errors', 'msgs'));
	}

	/**
	 * Update the selected vessel.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateVessel(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$vesselId = intval($request->get('vesselId'));
		// Vessel id is null when in add mode
		$mode = (isset($vesselId) && $vesselId > 0) ? 'edit': 'add';
		try {
			$vessel = Vessel::getVessel($vesselId);
			$vessel->name = $request->get('vesselName');
			$vessel->length = $request->get('length');
			$vessel->points = $request->get('points');
			$vessel->save();

		} catch(Exception $e) {
			Log::notice("Error updating vessel: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/vessels');
	}

}
