<?php

namespace App;

use App\FleetTemplate;
use App\FleetVessel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Fleet extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Creates a fleet from the fleet template for a game/user
     */
    public static function createFleet($gameId, $userId)
    {
        $fleet = new Fleet();
        $fleet->user_id = $userId;
        $fleet->game_id = $gameId;
        $fleet->save();
        // Create a fleet from the template set of vessels
        $vessels = FleetTemplate::select(array('vessel_id',))->orderBy("id")->get();

        // For each vessel in the template create a fleet vessel for each fleet
        foreach ($vessels as $vessel) {
            $fleetVessel = new FleetVessel();
            $fleetVessel->fleet_id = $fleet->id;
            $fleetVessel->vessel_id = $vessel->vessel_id;
            $fleetVessel->status = FleetVessel::FLEET_VESSEL_AVAILABLE;
            $fleetVessel->save();
        }
    }

    /**
     * Retrieve fleet object for fleet id
     */
    public static function getFleetById($fleetId)
    {
        $builder = self::select(
            array(
                'fleets.id',
                'fleets.user_id',
                'fleets.game_id'
            )
        );

        $fleet = $builder
            ->where("fleets.id", "=", $fleetId);

        if (!isset($fleet) || $fleet->count() <= 0) {
            throw new Exception("Could not find fleet with fleet id '$fleetId'");
        }

        return $fleet->get()[0];
    }

    /**
     * Retrieve fleet object for a user/game combination
     */
    public static function getFleet($gameId, $userId)
    {
        $builder = self::select(
            array(
                'fleets.id',
                'fleets.user_id',
                'fleets.game_id'
            )
        );

        $fleet = $builder
            ->where("fleets.game_id", "=", $gameId)
            ->where("fleets.user_id", "=", $userId)->get();

        if (isset($fleet) && count($fleet) > 0) {
            return $fleet[0];
        }
        return null;
    }
    
    /**
     * Retrieve entire fleet details for a game
     */
    public static function getFleetDetails($gameId, $userId)
    {
        $builder = self::select(
            array(
                'fleets.id',
                'fleets.user_id',
                'fleets.game_id',
                'users.name as user_name',
                'fleet_vessels.id as fleet_vessel_id',
                'fleet_vessels.status',
                'vessels.name as vessel_name',
                'vessels.length',
                'vessels.points',
            )
        )
            ->join('users', 'users.id', '=', 'fleets.user_id')
            ->join('fleet_vessels', 'fleet_vessels.fleet_id', '=', 'fleets.id')
            ->join('vessels', 'vessels.id', '=', 'fleet_vessels.vessel_id')
            ->orderBy("vessels.points", "DESC")
            ->orderBy("vessels.name");

        $builder
            ->where("fleets.user_id", "=", $userId)
            ->where("fleets.game_id", "=", $gameId);

        $fleet = $builder->get();

        if (!isset($fleet) || $fleet->count() <= 0) {
            throw new Exception("Could not find fleet with game id '$gameId' and user id '$userId'");
        }

        if (isset($fleet) && count($fleet) > 0) {
            foreach($fleet as &$fleetVessel) {
                $locations = FleetVesselLocation::getFleetVesselLocations($fleetVessel->fleet_vessel_id);
                $fleetVesselLocations[] = $locations;
                $fleetVessel->locations = $locations->toArray();
            }
        }

        return $fleet;
    }

    /**
     * Checks the fleet vessels, if they are all plotted then this fleet is ready
     */
    public static function isFleetReady($gameId, $userId)
    {
        $fleet = self::getFleet($gameId, $userId);
        if (isset($fleet)) {
            // Check if there are any fleet vessels which aren't in the plotted status
            $results = FleetVessel::where("fleet_vessels.fleet_id", "=", $fleet->id)
                ->where("fleet_vessels.status", "!=", FleetVessel::FLEET_VESSEL_PLOTTED)->get();

            return (count($results) <= 0);
        }

        return false;
    }

    /**
     * Checks the fleet vessels, if none are started or plotted then this fleet is not started
     */
    public static function isFleetNotStarted($gameId, $userId)
    {
        $fleet = self::getFleet($gameId, $userId);
        if (isset($fleet)) {
            // Check if there are any fleet vessels which are started or plotted status
            $builder = FleetVessel::where("fleet_vessels.fleet_id", "=", $fleet->id);
            $builder = $builder->where("fleet_vessels.status", "=", FleetVessel::FLEET_VESSEL_STARTED)
                            ->orWhere("fleet_vessels.status", "=", FleetVessel::FLEET_VESSEL_PLOTTED);

            return (count($builder->get()) <= 0);
        }

        return true;
    }

}