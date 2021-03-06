<?php
/**
 * Home Controller
 *
 * Used for the TV view
 * @author       Sander van Kasteel <info@sandervankasteel.n>
 * @author       Melle Dijkstra
 * @author       Rik van den Top
 */

namespace App\Http\Controllers;

use App\Models\Colloquium;
use Carbon\Carbon;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{

    /**
     * This is the action which will display the TV screen with relevant colloquia for this location
     * @param null $location_id The location to filter on
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tv($location_id = null) {

        $query = Colloquium::query();

        // If user filled in location then filter on it
        if(is_numeric($location_id)) {
            $query->join('rooms', 'rooms.id', '=', 'colloquia.room_id')
                ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                ->join('locations', 'locations.id', '=', 'buildings.location_id')
                ->where('locations.id','=',$location_id);
        };

        // Only get approved colloquia
        $query->where('approved',1);
        // Get upcoming colloquia first
        $query->whereDate('start_date', '>=', Carbon::now()->format('Y-m-d'));
        $query->orderBy('start_date', 'asc');
        $query->with(['user','room','room.building','language']);

        // Return first 20 colloquia to view
        $colloquia = $query->take(8)->get();

        return view('tv/tv', [
            'colloquia' => $colloquia,
        ]);
    }
}
