<?php

namespace App\Http\Controllers\Auth;

use App\Message;
use App\MessageText;
use App\User;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Guard;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->middleware('guest', ['except' => 'getLogout']);

        $this->auth = $auth;
    }

    /**
     * Show the login page to the user.
     *
     * @param Request $request
     * @return Response
     */
    public function getLogin(Request $request)
    {
        $loggedIn = false;
        if ($this->auth->check()) {
            $loggedIn = true;
        }

        //dd(Hash::make('battle101'));

        $errors = [];
        $msgs = [];
        $userName = '';

        return view('auth.login', compact('loggedIn', 'userName', 'errors', 'msgs'));
    }

    /**
     * If valid login show the application dashboard to the user.
     *
     * @param Request $request
     * @return Response
     */
    public function postLogin(Request $request)
    {
        $loggedIn = false;
        if ($this->auth->check()) {
            $loggedIn = true;
        }
        $errors = [];
        $msgs = [];

        $userName = trim($request->get('userName'));
        $password = trim($request->get('password'));

        $redirect = self::loggingIn($userName, $password);
        if (false != $redirect) {
            // Authentication passed...
            return redirect()->intended($redirect);
        }

        $errors[] = 'User name not found or an incorrect password was used.';
        return view('auth.login', compact('loggedIn', 'userName', 'errors', 'msgs'));
    }

    /**
     * Show the register page to the user.
     *
     * @param Request $request
     * @return Response
     */
    public function getRegister(Request $request)
    {
        //dd(Hash::make('battle101'));

        $errors = [];
        $msgs = [];
        $userName = '';
        // Generate a 4 digit random number.  Wwe will use the 3rd digit to choose an obfuscated image.
        $obfNumber = rand(1289, 8056);

        return view('auth.register', compact('userName', 'obfNumber', 'errors', 'msgs'));
    }


    /**
     * If valid register show the application home page to the user.
     *
     * @param Request $request
     * @return Response
     */
    public function postRegister(Request $request)
    {
        Log::info('postRegister: ' . $request->get('obfNumber'));
        Log::info('postRegister ary: ' . print_r($request->all(), true));

        if ($this->auth->check()) {
            // User is already logged in
            return redirect()->intended('/home');
        }
        $error = false;
        $errors = [];
        $msgs = [];
        $obfNumber = $request->get('obfNumber');

        try {
            $userName = trim($request->get('userName'));
            if (!isset($userName) || '' == $userName) {
                $errors[] = 'User name is required';
                $error = true;
            } elseif (strlen($userName) < User::USR_MIN_LEN) {
                $errors[] = 'User name must be at least ' . User::USR_MIN_LEN . ' in length';
                $error = true;
            }
            $password = trim($request->get('password'));
            if (!isset($password) || '' == $password) {
                $errors[] = 'Password is required';
                $error = true;
            } elseif (strlen($password) < User::PWD_MIN_LEN) {
                $errors[] = 'Password must be at least ' . User::PWD_MIN_LEN . ' in length';
                $error = true;
            }
            $displayedNumber = trim($request->get('displayedNumber'));
            if (!isset($displayedNumber) || '' == $displayedNumber) {
                $errors[] = 'The displayed number is required';
                $error = true;
            } elseif (false == self::checkCaptchaImageNumber($obfNumber, $displayedNumber)) {
                $errors[] = 'The displayed number was incorrect. Are you human?';
                $error = true;
            }

            if (false == $error)
            {
                // Get a new user object and create it
                $user = User::getUser();
                $user->name = $userName;
                $user->password = Hash::make($password);
                $user->password_hint = trim($request->get('passwordHint'));
                // Token required for API calls
                $user->user_token = User::getNewToken();
                $user->save();

                // New user, log them in
                $redirect = self::loggingIn($userName, $password);
                if ($redirect) {
                    // Authentication passed...
                    return redirect()->intended($redirect);
                }
            }

        } catch(QueryException $e) {
            $msg = $e->getMessage();
            if (starts_with($msg, 'SQLSTATE[23000]')) {
                Log::info($msg);
                // User name already exists in the database
                $errors[] = 'User name must be unique. Please choose another name.';
                $error = true;
            } else {
                // Some other SQL error
                $errors[] = $msg;
                $error = true;
            }
        } catch(Exception $e) {
            $errors[] = $e->getMessage();
            $error = true;
        }

        if (true == $error) {
            return view('auth.register', compact('userName', 'obfNumber', 'errors', 'msgs'));
        }

        return redirect()->intended('/auth/login');
    }

    /**
     * We are logging the user in, from register or login page.
     * @param $userName
     * @param $password
     * @return bool|string
     */
    private function loggingIn($userName, $password)
    {
        if (Auth::attempt(['name' => $userName, 'password' => $password])) {
            $user = $this->auth->user();
            // We place the user token in the response so it can be obtained
            // by the client and stored in a cookie
            setSessionVariable(self::SESSION_VAR_USER_TOKEN, $user->user_token);

            // Authentication passed, but check if we are dealing with a player two logging in
            $game = getSessionVariable(self::SESSION_VAR_GAME_TOKEN, false);
            if ($game) {
                if ($game->player_one_id == $user->id) {
                    // The user is trying to play against themselves.  Ignore the request and go home.
                    $messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_PLAYER_TWO_ERROR,
                        [User::getUser($game->player_one_id)->name, $game->name, User::systemUser()->name]);
                    Message::addMessage($messageText, User::systemUser()->id, $game->player_one_id);
                    // We clear the session variable so that the game token link must be used once more
                    setSessionVariable(self::SESSION_VAR_GAME_TOKEN, null);
                } else {
                    if ($game->player_two_id == $user->id) {
                        // The player 2 has already been set, just go to edit
                        return ('/editGrid?gameId=' . $game->id);
                    } else {
                        // Set the player 2 and go through the accept route, which creates the fleet
                        $game->setPlayerTwoForGame($user->id);

                        return ('/acceptGame?gameId=' . $game->id);
                    }
                }
            }

            // An authenticated user
            return '/home';
        }

        return false;
    }
}
