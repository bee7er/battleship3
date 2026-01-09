<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//Log:info('in routes');
/* Games section */
Route::get('/auth/login', 'Auth\AuthController@getLogin');
Route::post('/auth/login', 'Auth\AuthController@postLogin');
Route::get('/auth/register', 'Auth\AuthController@getRegister');
Route::post('/auth/register', 'Auth\AuthController@postRegister');
Route::get('/auth/logout', 'Auth\AuthController@getLogout');

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::post('/home', 'HomeController@index');

Route::get('/error', 'HomeController@error');
Route::get('/about', 'HomeController@about');
Route::get('/playerTwo', 'HomeController@playerTwo');
Route::get('/profile', 'HomeController@profile');
Route::post('/updateProfile', 'HomeController@updateProfile');
Route::get('/games', 'GamesController@index');
Route::get('/addGame', 'GamesController@addGame');
Route::post('/editGame', 'GamesController@editGame');
Route::get('/editGrid', 'GamesController@editGrid');
Route::get('/playGrid', 'GamesController@playGrid');
Route::get('/editGame', 'GamesController@editGame');
Route::get('/acceptGame', 'GamesController@acceptGame');
Route::post('/updateGame', 'GamesController@updateGame');
Route::get('/replay', 'GamesController@replay');
Route::get('/deleteGame', 'GamesController@deleteGame');

Route::get('/leaderboard', 'LeaderboardController@index');

/* API functions */
Route::get('/getCaptchaImage', 'API\SmbApiController@getCaptchaImage');
Route::post('/isUserNameUnique', 'API\SmbApiController@isUserNameUnique');
Route::post('/getPasswordHint', 'API\SmbApiController@getPasswordHint');
Route::post('/setVesselLocation', 'API\SmbApiController@setVesselLocation');
Route::post('/removeVesselLocation', 'API\SmbApiController@removeVesselLocation');
Route::post('/getGameStatus', 'API\SmbApiController@getGameStatus');
Route::post('/markAsRead', 'API\SmbApiController@markAsRead');
Route::post('/getLatestOpponentMove', 'API\SmbApiController@getLatestOpponentMove');
Route::post('/strikeVesselLocation', 'API\SmbApiController@strikeVesselLocation');
Route::post('/replaceFleetVesselLocations', 'API\SmbApiController@replaceFleetVesselLocations');
Route::post('/removeAllVesselLocations', 'API\SmbApiController@removeAllVesselLocations');

/* Admin section */
Route::get('/admin/dashboard', 'Admin\AdminController@index');
Route::get('/admin/users', 'Admin\UsersController@index');
Route::get('/admin/editUser', 'Admin\UsersController@editUser');
Route::get('/admin/addUser', 'Admin\UsersController@addUser');
Route::post('/admin/updateUser', 'Admin\UsersController@updateUser');

Route::get('/admin/fleetTemplates', 'Admin\FleetTemplatesController@index');
Route::get('/admin/editFleetTemplate', 'Admin\FleetTemplatesController@editFleetTemplate');
Route::get('/admin/addFleetTemplate', 'Admin\FleetTemplatesController@addFleetTemplate');
Route::post('/admin/updateFleetTemplate', 'Admin\FleetTemplatesController@updateFleetTemplate');
Route::get('/admin/deleteFleetTemplate', 'Admin\FleetTemplatesController@deleteFleetTemplate');

Route::get('/admin/messageTexts', 'Admin\MessageTextsController@index');
Route::get('/admin/editMessageText', 'Admin\MessageTextsController@editMessageText');
Route::get('/admin/addMessageText', 'Admin\MessageTextsController@addMessageText');
Route::post('/admin/updateMessageText', 'Admin\MessageTextsController@updateMessageText');
Route::get('/admin/deleteMessageText', 'Admin\MessageTextsController@deleteMessageText');

Route::get('/admin/vessels', 'Admin\VesselsController@index');
Route::get('/admin/editVessel', 'Admin\VesselsController@editVessel');
Route::get('/admin/addVessel', 'Admin\VesselsController@addVessel');
Route::post('/admin/updateVessel', 'Admin\VesselsController@updateVessel');
Route::get('/admin/deleteVessel', 'Admin\VesselsController@deleteVessel');

Route::get('/admin/games', 'Admin\GamesController@index');
Route::get('/admin/editGame', 'Admin\GamesController@editGame');
Route::get('/admin/addGame', 'Admin\GamesController@addGame');
Route::post('/admin/updateGame', 'Admin\GamesController@updateGame');
Route::get('/admin/deleteGame', 'Admin\GamesController@deleteGame');
Route::get('/admin/undeleteGame', 'Admin\GamesController@undeleteGame');