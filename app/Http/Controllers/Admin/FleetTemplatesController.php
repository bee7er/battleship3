<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\FleetTemplate;
use App\Vessel;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class FleetTemplatesController
 * @package App\Http\Controllers
 */
class FleetTemplatesController extends Controller
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
	 * Show all FleetTemplates
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

		$fleetTemplates = FleetTemplate::getFleetTemplates();

		return view('pages.admin.fleetTemplates.index', compact('loggedIn', 'fleetTemplates', 'errors', 'msgs'));
	}

	/**
	 * Add a new Fleet Template.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addFleetTemplate(Request $request)
	{
		return $this->editFleetTemplate($request);
	}

	/**
	 * Edit the selected Fleet Template.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editFleetTemplate(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$fleetTemplateId = $request->get('fleetTemplateId');
		$mode = 'edit';
		if (!$fleetTemplateId) {
			$mode = 'add';
		}

		$errors = [];
		$msgs = [];

		$fleetTemplate = null;
		$vessels = null;
		try {
			$fleetTemplate = FleetTemplate::getFleetTemplate($fleetTemplateId);
			$vessels = Vessel::getVessels();
		} catch(Exception $e) {
			Log::notice("Error getting fleet template for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.admin.fleetTemplates.editFleetTemplate', compact('loggedIn', 'fleetTemplate', 'vessels', 'mode', 'errors', 'msgs'));
	}

	/**
	 * Update the selected fleet template.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateFleetTemplate(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$fleetTemplateId = intval($request->get('fleetTemplateId'));
		// FleetTemplate id is null when in add mode
		$mode = (isset($fleetTemplateId) && $fleetTemplateId > 0) ? 'edit': 'add';
		try {
			$fleetTemplate = FleetTemplate::getFleetTemplate($fleetTemplateId);
			$fleetTemplate->vessel_id = $request->get('vesselId');
			$fleetTemplate->save();

		} catch(Exception $e) {
			Log::notice("Error updating flet template: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/fleetTemplates');
	}

	/**
	 * Delete the selected fleet template.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function deleteFleetTemplate(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$fleetTemplateId = intval($request->get('fleetTemplateId'));
		try {
			$fleetTemplate = FleetTemplate::getFleetTemplate($fleetTemplateId);
			$fleetTemplate->delete();

		} catch(Exception $e) {
			Log::notice("Error deleting fleet template: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/fleetTemplates');
	}

}
