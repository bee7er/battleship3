<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FleetTemplate extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vessel_id'];

    /**
     * Retrieve a user
     */
    public static function getFleetTemplate($id=null)
    {
        if (null == $id) {
            // Add mode
            return new FleetTemplate();
        }

        $builder = self::select(
            array(
                'fleet_templates.id',
                'fleet_templates.vessel_id',
                'vessels.name as vessel_name'
            )
        )
            ->where("fleet_templates.id", "=", $id)
            ->join('vessels', 'vessels.id', '=', 'fleet_templates.vessel_id');

        return $builder->get()[0];
    }

    /**
     * Retrieve all fleet template entries
     *
     * @return mixed
     */
    public static function getFleetTemplates()
    {
        $builder = self::select(
            array(
                'fleet_templates.id',
                'fleet_templates.vessel_id',
                'vessels.name as vessel_name'
            )
        )
            ->join('vessels', 'vessels.id', '=', 'fleet_templates.vessel_id')
            ->orderBy("vessels.name");

        return $builder->get();
    }

    /**
     * Get the fleet vessel size and their lengths, to return the fleet location size
     */
    public static function getFleetLocationSize()
    {
        $builder = self::select(
            array(
                'fleet_templates.id',
                'fleet_templates.vessel_id',
                'vessels.length'
            )
        )
            ->join('vessels', 'vessels.id', '=', 'fleet_templates.vessel_id');

        $fleetTemplates = $builder->get();
        $fleetLocationSize = 0;
        if (isset($fleetTemplates) && $fleetTemplates->count() > 0) {
            foreach ($fleetTemplates as $fleetTemplate) {
                $fleetLocationSize += $fleetTemplate->length;
            }
        }

        return $fleetLocationSize;
    }
}