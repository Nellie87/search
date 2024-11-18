<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\County;
use App\Models\Subcounty;
use App\Models\Location;
use App\Models\Sublocation;

class LocationController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $countryId = $request->input('country_id');
        $countyId = $request->input('county_id');
        $subcountyId = $request->input('subcounty_id');
        $locationId = $request->input('location_id'); // New filter for sublocations
    
        $countries = Country::where('name', 'LIKE', "%$query%")->get();
    
        $counties = County::when($countryId, function ($query, $countryId) {
            return $query->where('country_id', $countryId);
        })->where('name', 'LIKE', "%$query%")->get();
    
        $subcounties = Subcounty::when($countyId, function ($query, $countyId) {
            return $query->where('county_id', $countyId);
        })->where('name', 'LIKE', "%$query%")->get();
    
        $locations = Location::when($subcountyId, function ($query, $subcountyId) {
            return $query->where('subcounty_id', $subcountyId);
        })->where('name', 'LIKE', "%$query%")->get();
    
        $sublocations = Sublocation::when($locationId, function ($query, $locationId) {
            return $query->where('location_id', $locationId);
        })->where('name', 'LIKE', "%$query%")->get();
    
        return response()->json([
            'countries' => $countries,
            'counties' => $counties,
            'subcounties' => $subcounties,
            'locations' => $locations,
            'sublocations' => $sublocations,
        ]);
    }
    

    
    
    public function searchLocations(Request $request)
    {
        $query = $request->input('query');
    
        if ($query) {
            // Search for subcounties and locations
            $subcounties = Subcounty::with('county.country')
                ->where('name', 'LIKE', "%$query%")
                ->get();
    
            $locations = Location::with('subcounty.county.country')
                ->where('name', 'LIKE', "%$query%")
                ->get();
    
            // Format results
            $results = $subcounties->map(function ($subcounty) {
                return [
                    'type' => 'subcounty',
                    'name' => $subcounty->name,
                    'county' => $subcounty->county->name ?? 'Unknown',
                    'country' => $subcounty->county->country->name ?? 'Unknown',
                ];
            })->merge($locations->map(function ($location) {
                return [
                    'type' => 'location',
                    'name' => $location->name,
                    'subcounty' => $location->subcounty->name ?? 'Unknown',
                    'county' => $location->subcounty->county->name ?? 'Unknown',
                    'country' => $location->subcounty->county->country->name ?? 'Unknown',
                ];
            }));
    
            return response()->json($results);
        }
    
        return response()->json([
            'message' => 'Please provide a search query.',
        ], 400);
    }
    
    public function traceHierarchy(Request $request)
    {
        $query = $request->input('query');
    
        if (!$query) {
            return response()->json(['message' => 'Please provide a query.'], 400);
        }
    
        // Search logic for each level
        $sublocation = Sublocation::with('location.subcounty.county.country')
            ->where('name', 'LIKE', "%$query%")
            ->first();
    
        if ($sublocation) {
            return response()->json([
                'level' => 'sublocation',
                'sublocation' => $sublocation->name,
                'location' => $sublocation->location->name ?? 'Unknown',
                'subcounty' => $sublocation->location->subcounty->name ?? 'Unknown',
                'county' => $sublocation->location->subcounty->county->name ?? 'Unknown',
                'country' => $sublocation->location->subcounty->county->country->name ?? 'Unknown',
            ]);
        }
    
        $location = Location::with('subcounty.county.country')
            ->where('name', 'LIKE', "%$query%")
            ->first();
    
        if ($location) {
            return response()->json([
                'level' => 'location',
                'sublocation' => 'Unknown',
                'location' => $location->name,
                'subcounty' => $location->subcounty->name ?? 'Unknown',
                'county' => $location->subcounty->county->name ?? 'Unknown',
                'country' => $location->subcounty->county->country->name ?? 'Unknown',
            ]);
        }
    
        $subcounty = Subcounty::with('county.country')
            ->where('name', 'LIKE', "%$query%")
            ->first();
    
        if ($subcounty) {
            return response()->json([
                'level' => 'subcounty',
                'sublocation' => 'Unknown',
                'location' => 'Unknown',
                'subcounty' => $subcounty->name,
                'county' => $subcounty->county->name ?? 'Unknown',
                'country' => $subcounty->county->country->name ?? 'Unknown',
            ]);
        }
    
        $county = County::with('country')
            ->where('name', 'LIKE', "%$query%")
            ->first();
    
        if ($county) {
            return response()->json([
                'level' => 'county',
                'sublocation' => 'Unknown',
                'location' => 'Unknown',
                'subcounty' => 'Unknown',
                'county' => $county->name,
                'country' => $county->country->name ?? 'Unknown',
            ]);
        }
    
        $country = Country::where('name', 'LIKE', "%$query%")
            ->first();
    
        if ($country) {
            return response()->json([
                'level' => 'country',
                'sublocation' => 'Unknown',
                'location' => 'Unknown',
                'subcounty' => 'Unknown',
                'county' => 'Unknown',
                'country' => $country->name,
            ]);
        }
    
        return response()->json(['message' => 'No matching records found.'], 404);
    }
    
    

}
