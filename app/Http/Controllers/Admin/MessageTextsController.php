<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MessageText;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class MessageTextsController
 * @package App\Http\Controllers
 */
class MessageTextsController extends Controller
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
	 * Show all MessageTexts
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

		$messageTexts = MessageText::getBroadcastMessages($status=null);

		return view('pages.admin.messageTexts.index', compact('loggedIn', 'messageTexts', 'errors', 'msgs'));
	}

	/**
	 * Add a new message text.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function addMessageText(Request $request)
	{
		return $this->editMessageText($request);
	}

	/**
	 * Edit the selected message text.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function editMessageText(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$loggedIn = true;
		$messageTextId = $request->get('messageTextId');
		$mode = 'edit';
		if (!$messageTextId) {
			$mode = 'add';
		}

		$errors = [];
		$msgs = [];

		$messageText = null;
		try {
			$messageText = MessageText::getMessageText($messageTextId);
			$statuses = MessageText::STATUS_ARRAY;

		} catch(Exception $e) {
			Log::notice("Error getting message text for edit: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
			$errors[] = $e->getMessage();
		}

		return view('pages.admin.messageTexts.editMessageText', compact('loggedIn', 'messageText', 'statuses', 'mode', 'errors', 'msgs'));
	}

	/**
	 * Update the selected message text.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function updateMessageText(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$messageTextId = intval($request->get('messageTextId'));
		// MessageText id is null when in add mode
		$mode = (isset($messageTextId) && $messageTextId > 0) ? 'edit': 'add';
		try {
			$messageText = MessageText::getMessageText($messageTextId);
			$messageText->name = $request->get('name');
			$messageText->text = $request->get('text');
			$messageText->status = $request->get('status');
			$messageText->type = MessageText::TYPE_BROADCAST;
			$messageText->save();

		} catch(Exception $e) {
			Log::notice("Error updating user: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/messageTexts');
	}

	/**
	 * Delete the selected message text.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function deleteMessageText(Request $request)
	{
		if (!$this->auth->check() || $this->auth->user()->admin != 1) {
			return redirect()->intended('error');
		}

		$messageTextId = intval($request->get('messageTextId'));
		try {
			$messageText = MessageText::getMessageText($messageTextId);
			$messageText->delete();

		} catch(Exception $e) {
			Log::notice("Error updating message text: {$e->getMessage()} at {$e->getFile()}, {$e->getLine()}");
		}

		return redirect()->intended('/admin/messageTexts');
	}

}
