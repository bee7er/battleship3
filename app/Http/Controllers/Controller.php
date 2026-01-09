<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SESSION_VAR_GAME_TOKEN = 'PlayerTwoLinkToken';
    const SESSION_VAR_USER_TOKEN = 'UserToken';
    const OBFUSCATED_NUMBER_FILES = ['deux.jpeg','zero.jpeg','neuf.jpeg','quartre.jpeg','un.jpeg','huit.jpeg','cinq.jpeg','trois.jpeg','six.jpeg','sept.jpeg'];
    const OBFUSCATED_NUMBER_FILES_ORDERED = ['zero.jpeg','un.jpeg','deux.jpeg','trois.jpeg','quartre.jpeg','cinq.jpeg','six.jpeg','sept.jpeg','huit.jpeg','neuf.jpeg'];

    /**
     * Set a cookie to a given value
     * @param $cookieName
     * @param $value
     */
    public static function setCookie($cookieName, $value)
    {
        $_COOKIE[$cookieName] = $value;
    }

    /**
     * Retrieve a cookie value providing a default
     * @param $cookieName
     * @param $default
     * @return mixed
     */
    public static function getCookie($cookieName, $default)
    {
        $value = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName]: $default;
        return $value;
    }

    /**
     * Chooses an image pseudo-randomly from the array and returns it.
     * This forms the basis of a test that the submission comes from a human.
     *
     * @param $obfNumber
     * @return mixed
     */
    public static function getCaptchaImageFileName($obfNumber)
    {
        // The $obfNumber was generated randomly and we use the 3rd number to select the image
        $obfDigit = substr("$obfNumber", 2, 1);
        $file = self::OBFUSCATED_NUMBER_FILES[$obfDigit];

        return $file;
    }

    /**
     * Retrieves the obfuscated image file, gets the index of that file in the
     * proper sequence and compares that index with the number given by the user.
     *
     * @param $obfNumber
     * @return mixed
     */
    public static function checkCaptchaImageNumber($obfNumber, $displayedNumber)
    {
        $fileName = self::getCaptchaImageFileName($obfNumber);
        $index = array_search($fileName, self::OBFUSCATED_NUMBER_FILES_ORDERED);

//        Log::info("Displayed: $displayedNumber, and Index: $index");

        return ($displayedNumber == $index);
    }
}
