<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\County;
use App\Models\Subcounty;

class LocationController extends Controller
{
   public function search(Request $request)
{
    $query = $request->input('query');
    $countryId = $request->input('country_id');
    $countyId = $request->input('county_id');

    $countries = Country::where('name', 'LIKE', "%$query%")->get();

    $counties = County::when($countryId, function ($query, $countryId) {
        return $query->where('country_id', $countryId);
    })->where('name', 'LIKE', "%$query%")->get();

    $subcounties = Subcounty::when($countyId, function ($query, $countyId) {
        return $query->where('county_id', $countyId);
    })->where('name', 'LIKE', "%$query%")->get();

    return response()->json([
        'countries' => $countries,
        'counties' => $counties,
        'subcounties' => $subcounties,
    ]);
}public function searchLocations(Request $request)
{
    $query = $request->get('query');

    // Search for countries
    $countries = Country::where('name', 'LIKE', "%$query%")->get();

    // Search for counties (and their countries)
    $counties = County::with('country') // Include the related country
        ->where('name', 'LIKE', "%$query%")
        ->orWhereHas('country', function($q) use ($query) {
            $q->where('name', 'LIKE', "%$query%");
        })->get();

    // Search for subcounties (and their counties + countries)
    $subcounties = Subcounty::with(['county.country']) // Include the related county and country
        ->where('name', 'LIKE', "%$query%")
        ->orWhereHas('county.country', function($q) use ($query) {
            $q->where('name', 'LIKE', "%$query%");
        })->get();

    return response()->json([
        'countries' => $countries,
        'counties' => $counties,
        'subcounties' => $subcounties,
    ]);
}


}
