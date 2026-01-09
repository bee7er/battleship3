<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FleetVessel extends Model
{
    const FLEET_VESSEL_AVAILABLE = 'available';
    const FLEET_VESSEL_STARTED = 'started';
    const FLEET_VESSEL_PLOTTED = 'plotted';
    const FLEET_VESSEL_HIT = 'hit';
    const FLEET_VESSEL_DESTROYED = 'destroyed';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_vessels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fleet_id', 'vessel_id', 'status'];

    /**
     * Retrieve fleet vessel object
     */
    public static function getFleetVessel($fleetVesselId)
    {
        $builder = self::select(
            array(
                'fleet_vessels.id',
                'fleet_vessels.fleet_id',
                'fleet_vessels.vessel_id',
                'fleet_vessels.status',
                'vessels.name as vessel_name',
                'vessels.length',
                'vessels.points'
            )
        )
            ->join('vessels', 'vessels.id', '=', 'fleet_vessels.vessel_id');

        $fleetVessel = $builder
            ->where("fleet_vessels.id", "=", $fleetVesselId);

        if (!isset($fleetVessel) || $fleetVessel->count() <= 0) {
            throw new Exception("Could not find fleet vessel with fleet vessel id '$fleetVesselId'");
        }

        if ($fleetVessel->count() > 1) {
            throw new Exception("More than one fleet vessel with fleet vessel id '$fleetVesselId'");
        }

        return $fleetVessel->get()[0];
    }

    /**
     * Retrieve entire set of fleet vessel location
     */
    public static function getFleetVesselLocationByRowCol($row, $col, $fleetId)
    {
        $builder = self::select(
            array(
                'fleet_vessels.fleet_id',
                'fleet_vessel_locations.id as fleet_vessel_location_id',
                'fleet_vessel_locations.fleet_vessel_id',
                'fleet_vessel_locations.move_id',
                'fleet_vessel_locations.row',
                'fleet_vessel_locations.col',
                'fleet_vessel_locations.status as vessel_location_status',
            )
        )
            ->join('fleet_vessel_locations', 'fleet_vessel_locations.fleet_vessel_id', '=', 'fleet_vessels.id')
            ->where("fleet_vessels.fleet_id", "=", $fleetId)
            ->where("fleet_vessel_locations.row", "=", $row)
            ->where("fleet_vessel_locations.col", "=", $col);

        $fleetVesselLocations = $builder->get();

        if (isset($fleetVesselLocations) && count($fleetVesselLocations) > 0) {
            return $fleetVesselLocations[0];
        }

        return null;
    }

    /**
     * Retrieve entire set of fleet vessel locations
     */
    public static function getAllFleetVesselLocations($fleetId)
    {
        $builder = self::select(
            array(
                'fleet_vessels.fleet_id',
                'fleet_vessel_locations.id as fleet_vessel_location_id',
                'fleet_vessel_locations.fleet_vessel_id',
                'fleet_vessel_locations.row',
                'fleet_vessel_locations.col',
                'fleet_vessel_locations.status as vessel_location_status',
            )
        )
            ->join('fleet_vessel_locations', 'fleet_vessel_locations.fleet_vessel_id', '=', 'fleet_vessels.id')
            ->where("fleet_vessels.fleet_id", "=", $fleetId);

        $fleetVesselLocations = $builder->get();

        if (isset($fleetVesselLocations) && count($fleetVesselLocations) > 0) {
            return $fleetVesselLocations;
        }

        return null;
    }

    /**
     * Retrieve entire set of fleet vessel locations and checks their status
     */
    public static function isFleetDestroyed($fleetId)
    {
        // Check the fleet vessel locations to see if all parts of all vessels have been destroyed
        $fleetVesselLocations = self::getAllFleetVesselLocations($fleetId);
        $atLeastOneIntact = false;
        if (isset($fleetVesselLocations) && count($fleetVesselLocations) > 0) {
            foreach ($fleetVesselLocations as $location) {
                if (FleetVesselLocation::FLEET_VESSEL_LOCATION_NORMAL == $location->vessel_location_status
                    || FleetVesselLocation::FLEET_VESSEL_LOCATION_HIT == $location->vessel_location_status) {
                    // At least one part of one vessel remains intact
                    $atLeastOneIntact = true;
                    break;
                }
            }
        }

        return (!$atLeastOneIntact);
    }
}