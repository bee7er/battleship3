<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Game;
use App\Fleet;
use App\FleetVessel;
use App\FleetVesselLocation;
use App\Message;
use App\MessageText;
use App\Move;
use App\User;
use Exception;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * Class SmbApiController
 * @package App\Http\Controllers\API
 */
class SmbApiController extends Controller
{
	/**
	 * Create a new filter instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Chooses an image pseudo-randomly from the array and returns it.
	 * This forms the basis of a test that the submission comes from a human.
	 *
	 * @param Request $request
	 * @return mixed
	 */
	public function getCaptchaImage(Request $request)
	{
		$obfNumber = $request->get('n');
		$file = self::getCaptchaImageFileName($obfNumber);
		$fileName = (public_path() . "/images/$file");

		//Log::info($fileName);

		header('Content-Type', 'image/jpeg');
		return Response::download($fileName);
	}

	/**
	 * Check that a supplied user name is unique
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function isUserNameUnique(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$isUnique = null;
		$returnedData = null;

		try {
			$userName = $request->get('userName');
			$user = User::getUserByUserName($userName);
			if (isset($user) && null !== $user) {
				$message = "User name already exists";
				$isUnique = false;
			} else {
				$isUnique = true;
			}

			$returnedData = [
				"isUnique" => $isUnique
			];

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in isUserNameUnique(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Get the user's password hint
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function getPasswordHint(Request $request)
	{
		$message = "Data received OK";
		$result = null;
		$passwordHint = null;
		$returnedData = null;

		try {
			$userName = $request->get('userName');
			$error = '';
			$user = User::getUserByUserName($userName);
			if (isset($user) && null !== $user) {
				$message = "User name found ok";
				$passwordHint = $user->password_hint;;
			} else {
				$error = "User name not found";
			}

			$returnedData = [
				"passwordHint" => $passwordHint,
				"error" => $error
			];

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in getPasswordHint(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Mark a system message as having been read
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function markAsRead(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$messageId = $request->get('messageId');
			$message = Message::getMessage($messageId);
			$message->status = Message::STATUS_READ;
			$message->save();

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in getGameStatus(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Get Game current status
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function getGameStatus(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$gameStatus = 'unknown';
		$winnerId = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$gameId = $request->get('gameId');
			$game = Game::getGame($gameId);
			$gameStatus = $game->status;
			$winnerId = $game->winner_id;

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in getGameStatus(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => ['gameStatus' => $gameStatus, 'winnerId' => $winnerId]
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Save the locations of a vessel
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function setVesselLocation(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$fleetVessel = Input::all();

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			// Update the fleet vessel status, returned below
			$fleetVessel['status'] =
				FleetVesselLocation::addNewLocations($fleetVessel['fleetVesselId'], $fleetVessel['locations']);

			if (FleetVessel::FLEET_VESSEL_PLOTTED == $fleetVessel['status']) {
				// Are they all plotted?  If so, we set the game to waiting or ready.
				// However, care must be taken because the current user could be either
				// the player 1 or player 2.
				$game = Game::getGame($fleetVessel['gameId']);
				$game->status = Game::setGameStatus($game->id);
			}
			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in setVesselLocation(): ' . $message . ', line: ' . $exception->getLine());
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $fleetVessel
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Remove the locations of a vesssel
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function removeVesselLocation(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$location = Input::all();
		$fleetVessel = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			// We update the fleet vessel, returned below
			$fleetVessel = $location['fleetVessel'];
			$fleetVessel['status'] = FleetVesselLocation::deleteLocation($fleetVessel['fleetVesselId'], $location['row'], $location['col']);
			$fleetVessel['locations'] = [];
			// Replace the remaining locations, if any
			$locations = FleetVesselLocation::getFleetVesselLocations($fleetVessel['fleetVesselId']);
			if (isset($locations) && 0 < count($locations)) {
				foreach ($locations as $existingLocation) {
					$fleetVessel['locations'][] = [
						'id' => $existingLocation->id,
						'fleet_vessel_id' => $existingLocation->fleet_vessel_id,
						'row' => $existingLocation->row,
						'col' => $existingLocation->col,
						'vessel_name' => $existingLocation->vessel_name,
					];
				}
			}

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in removeVesselLocation(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $fleetVessel
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Remove the locations of a vesssel
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function removeAllVesselLocations(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$location = Input::all();
		$fleetVessel = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			// We update the fleet vessel, returned below
			$fleetVessel = $location['fleetVessel'];
			$fleetVessel['status'] = FleetVesselLocation::deleteAllLocations($fleetVessel['fleetVesselId']);
			$fleetVessel['locations'] = [];

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in removeVesselLocation(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $fleetVessel
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Find and return the latest move for the specified user
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function getLatestOpponentMove(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$returnedData = null;
		$affectedLocations = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			// We grab the latest move.  If it was by them, we gauge the impact on my fleet.
			$move = Move::getLatestMove($request->get('gameId'));

			if (isset($move) && $move->player_id == $request->get('userId'))
			{
				$locationHit = FleetVessel::getFleetVesselLocationByRowCol($move->row, $move->col, $request->get('fleetId'));
				if (isset($locationHit)) {
					// The strike has hit a vessel at that location.  Get all affected locations.
					$affectedLocations = $this->getAffectedLocations($locationHit);
				}

				$returnedData = [
					'move' => $move,
					'affectedLocations' => $affectedLocations
				];
			}

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in getLatestOpponentMove(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Strike a vessel location
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function strikeVesselLocation(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$returnedData = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$userId = $request->get('userId');
			$gameId = $request->get('gameId');
			$fleetId = $request->get('fleetId');
			$row = $request->get('row');
			$col = $request->get('col');

            list($move, $affectedLocations) = $this->processStrike($gameId, $fleetId, $userId, $row, $col);

			$returnedData = [
				'move' => $move,
				'affectedLocations' => $affectedLocations
			];
			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			Log::info('Error in strikeVesselLocation(): ' . $message);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * A strike has been made, process it.
	 */
	private function processStrike($gameId, $fleetId, $userId, $row, $col)
	{
        $affectedLocations = null;
        $move = null;
        // Check if this is the first move by this user for this game
        $userMoves = Move::getMoves($gameId, $userId);
        if (!isset($userMoves) || count($userMoves) <= 0) {
            // First move, bump up the game played counter
            User::addGameCount($userId);
        }
        // We save this latest move.
        $move = new Move();
        $move->game_id = $gameId;
        $move->player_id = $userId;
        $move->row = $row;
        $move->col = $col;
        $move->save();

        $locationHit = FleetVessel::getFleetVesselLocationByRowCol($move->row, $move->col, $fleetId);

        if (isset($locationHit)) {
            // Record the move that caused this hit
            $fvl = FleetVesselLocation::getFleetVesselLocationById($locationHit->fleet_vessel_location_id);
            $fvl->move_id = $move->id;
            $fvl->save();
            // The strike has hit a vessel at that location.  Get all affected locations.
            $affectedLocations = $this->getAffectedLocations($locationHit, $userId);
            // The move was successful, so record that against the move
            $move->hit_vessel = 1;
            $move->hit_fleet_vessel_id = $locationHit->fleet_vessel_id;
            $move->save();
        }

        // Check if all fleet vessels have been destroyed, derives the game status as it currently stands
        Game::setGameStatus($gameId);

        return [$move, $affectedLocations];
    }

	/**
	 * A successful hit, find and returned all affected locations
	 */
	private function getAffectedLocations($locationHit, $userId=null)
	{
		$fleetVessel = FleetVessel::getFleetVessel($locationHit->fleet_vessel_id);

		$affectedLocations = [];
		$affectedLocations[] = [
			'fleetVesselId' => $locationHit->fleet_vessel_id,
			'fleetVesselLocationId' => $locationHit->fleet_vessel_location_id,
			'status' => FleetVesselLocation::FLEET_VESSEL_LOCATION_HIT
		];
		// Update the status at that location
		$fleetVesselLocation = FleetVesselLocation::getFleetVesselLocationById($locationHit->fleet_vessel_location_id);
		$fleetVesselLocation->status = FleetVesselLocation::FLEET_VESSEL_LOCATION_HIT;
		$fleetVesselLocation->save();
		// Have all the vessel parts been hit
		$fleetVesselLocations = FleetVesselLocation::getFleetVesselLocationsByVesselId($fleetVesselLocation->fleet_vessel_id);
		$isDestroyed = true;
		foreach ($fleetVesselLocations as $fvl) {
			if ($fvl->status == FleetVesselLocation::FLEET_VESSEL_LOCATION_NORMAL) {
				$isDestroyed = false;
				break;
			}
		}

		if (true == $isDestroyed) {
			if (null != $userId) {
				// Vessel destroyed, bump up the destroyed and points counts
				User::addDestroyedCount($userId, $fleetVessel->points);
			}

			$affectedLocations = [];
			// Update the status of the various parts to destroyed
			foreach ($fleetVesselLocations as $fvl) {
				$fvl->status = FleetVesselLocation::FLEET_VESSEL_LOCATION_DESTROYED;
				$fvl->save();
				// Save all affected locations
				$affectedLocations[] = ['fleetVesselId' => $fvl->fleet_vessel_id, 'fleetVesselLocationId' => $fvl->id,
					'status' => FleetVesselLocation::FLEET_VESSEL_LOCATION_DESTROYED];
			}
			$fleetVessel->status = FleetVessel::FLEET_VESSEL_DESTROYED;
		} else {
			$fleetVessel->status = FleetVessel::FLEET_VESSEL_HIT;
		}
		$fleetVessel->save();

		return $affectedLocations;
	}

	/**
	 * Delete existing locations for each fleet vessel and replace them with those supplied
	 */
	public function replaceFleetVesselLocations(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$returnedData = null;
		$affectedLocations = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$gameId = $request->get('gameId');
			$fleetId = $request->get('fleetId');
			$fleetVessels = $request->get('fleetVessels');
			// Delete all locations for each vessel
			if (isset($fleetVessels) && count($fleetVessels) > 0) {
				foreach ($fleetVessels as $fleetVessel) {
					$locations = FleetVesselLocation::getFleetVesselLocationsByVesselId($fleetVessel['fleetVesselId']);
					if (isset($locations) && count($locations) > 0) {
						foreach ($locations as $location) {
							$location->delete();
						}
					}
				}
			}
			// Now add the new ones
			$fleetVesselCount = 0;
			$fleetVesselLocationCount = 0;
			if (isset($fleetVessels) && count($fleetVessels) > 0) {
				foreach ($fleetVessels as $fleetVessel) {
					if (isset($fleetVessel['locations']) && count($fleetVessel['locations']) > 0) {
						$fleetVesselCount += 1;
						$fleetVesselLocationCount += count($fleetVessel['locations']);
						FleetVesselLocation::addNewLocations($fleetVessel['fleetVesselId'], $fleetVessel['locations']);
					} else {
						$fleetVesselObj = FleetVessel::getFleetVessel($fleetVessel['fleetVesselId']);
						$fleetVesselObj->status = FleetVessel::FLEET_VESSEL_AVAILABLE;
						$fleetVesselObj->save();
					}
				}
			}

			// Check the fleet vessels of both combatants and set the game status
			Game::setGameStatus($gameId);

			$returnedData = [
				"fleetVesselCount" => $fleetVesselCount,
				"fleetVesselLocationCount" => $fleetVesselLocationCount
			];
			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			$file = $exception->getFile();
			$line = $exception->getLine();
			Log::info('Error in replaceFleetVesselLocations(): ' . $message . ', file: ' . $file . ', line: ' . $line);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * Set the game to single player mode
	 */
	public function setGameToSinglePlayer(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$error = null;
		$returnedData = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$gameId = $request->get('gameId');
			$game = Game::getGame($gameId);
			$fleet = [];

			if (isset($game) && null != $game)
			{
				if (Game::STATUS_WAITING != $game->status) {
					$error = "Game must be in a Waiting status in order to be single player";
				} elseif (null != $game->second_player_id) {
					$error = "The second player must not already be set in order to be single player";
				} else {
					$game->player_two_id = User::SYSTEM_USER_ID;
					$game->status = Game::STATUS_READY;
					$game->save();

					// Create a fleet from the template set of vessels for the System user
					Fleet::createFleet($gameId, User::SYSTEM_USER_ID);
					// Get the newly created fleet details
					$fleet = Fleet::getFleetDetails($gameId, User::SYSTEM_USER_ID);

					// Message player 1 that the game is accepted by System
					$messageText = MessageText::retrieveMessageText(MessageText::MESSAGE_ACCEPT,
						[
							User::getUser($game->player_one_id)->name,
							Game::getGame($game->id)->name,
							User::getUser($game->player_two_id)->name,
						]
					);
					Message::addMessage($messageText, $game->player_two_id, $game->player_one_id);
				}
			} else {
				$error = "Game not found for id '$gameId'";
			}

			$returnedData = [
				"fleet" => $fleet,
				"status" => $game->status,
				"error" => $error
			];

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			$file = $exception->getFile();
			$line = $exception->getLine();
			Log::info('Error in setGameToSinglePlayer(): ' . $message . ', file: ' . $file . ', line: ' . $line);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

	/**
	 * In single player mode, here we are striking the opponents fleet
	 */
	public function singlePlayerStrike(Request $request)
	{
		$message = "Data received OK";
		$result = 'Error';
		$error = null;
		$returnedData = null;

		try {
			// User token must be provided and valid for all API calls
			User::checkUserToken($request->get(User::USER_TOKEN));

			$gameId = $request->get('gameId');
			$game = Game::getGame($gameId);
            $fleet = Fleet::getFleet($gameId, $game->player_one_id);

			if (isset($game) && null != $game)
			{
                $allMoves = Move::getAllMovesBySystemUser($gameId);
                // Get the size of the opponent's smallest vessel which has not been hit
                $smallestVesselSize = FleetVessel::getFleetVesselMinimumLength($fleet->id);

                $singlePlayerHandler = new \SinglePlayerHandler();
                $singlePlayerHandler->setAllMoves($allMoves);
                $singlePlayerHandler->setSmallestVesselSize($smallestVesselSize);

                // Analyse all moves by the System and derive the next cell to hit
                $singlePlayerHandler->processSinglePlayerMoves();
                // Apply the next strike
                list($move, $affectedLocations) = $this->processStrike(
                    $gameId,
                    $fleet->id,
                    User::SYSTEM_USER_ID,
                    $singlePlayerHandler->getNextRow(),
                    $singlePlayerHandler->getNextCol()
                );

			} else {
				$error = "Game not found for id '$gameId'";
			}

			$returnedData = [
				"error" => $error
			];

			$result = 'OK';

		} catch(\Exception $exception) {
			$result = 'Error';
			$message = $exception->getMessage();
			$file = $exception->getFile();
			$line = $exception->getLine();
			Log::info('Error in singlePlayerStrike(): ' . $message . ', file: ' . $file . ', line: ' . $line);
		}

		$returnData = [
			"message" => $message,
			"result" => $result,
			"returnedData" => $returnedData
		];

		return $returnData;   // Gets converted to json
	}

}
