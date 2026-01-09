<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Vessel extends Model
{
    const VESSEL_TYPE_AIRCRAFT_CARRIER = 'aircraft-carrier';
    const VESSEL_TYPE_BATTLESHIP = 'battleship';
    const VESSEL_TYPE_CRUISER = 'cruiser';
    const VESSEL_TYPE_SUBMARINE = 'submarine';
    const VESSEL_TYPE_DESTROYER = 'destroyer';

    const VESSEL_PART_FORE = 'fore';
    const VESSEL_PART_MIDSHIP = 'midship';
    const VESSEL_PART_AFT = 'aft';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vessels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'length', 'points'];

    /**
     * Retrieve a vessel
     */
    public static function getVessel($id=null)
    {
        if (null == $id) {
            // Add mode
            return new Vessel();
        }

        return self::findOrFail($id);
    }

    /**
     * Retrieve all vessels
     *
     * @return mixed
     */
    public static function getVessels()
    {
        $builder = self::select('*')
            ->orderBy("vessels.name");

        return $builder->get();
    }
}