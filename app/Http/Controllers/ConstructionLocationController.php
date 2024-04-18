<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\ConstructionSite;
use App\Http\Controllers\Controller;
use App\Models\ConstructionLocation;
use App\Models\ConstructionMaterial;
use Illuminate\Support\Facades\Http;



class ConstructionLocationController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionLocation $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    public function CommetApi()
    {
       
        try {
            $this->saveNewCantiereCoordinates();
            // Step 1: Login and obtain the authentication token
            $client = new Client();
            $loginResponse = $client->post('https://almecdiag.net:9891/login', [
                'json' => [
                    'email' => 'greengengroupsrl@gmail.com',
                    'password' => 'Greengengroup!',
                ],
            ]);

            // Check if login was successful
            if ($loginResponse->getStatusCode() === 200) {
                $authToken = json_decode($loginResponse->getBody(), true)['token'];

                $startDate = Carbon::now()->subHours(24 * 1); // Current date and time
                $endDate = Carbon::now(); // Subtract 500 hours

                // Format dates in ISO8601 format
                $dtFrom = $startDate->format('Y-m-d\TH:i:s.v\Z');
                $dtTo = $endDate->format('Y-m-d\TH:i:s.v\Z');


                $positionsResponse = $client->get('https://almecdiag.net:9891/export/positions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $authToken,
                    ],
                    'query' => [
                        'c_machine' => '862493059018931',
                        'dt_from' => $dtFrom,
                        'dt_to' => $dtTo,
                    ],
                ]);

                // Check if fetching positions was successful
               
                if ($positionsResponse->getStatusCode() === 200) {
                    $positionsData = json_decode($positionsResponse->getBody(), true);


                    function haversine($lat1, $lon1, $lat2, $lon2)
                    {

                        $R = 6371000; // Earth radius in meters
                        $dlat = deg2rad($lat2 - $lat1);
                        $dlon = deg2rad($lon2 - $lon1);
                        $a = sin($dlat / 2) * sin($dlat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon / 2) * sin($dlon / 2);
                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                        $distance = $R * $c;

                        return $distance;
                    }

                    $matchingLocations = [];
                    $counter =  0;
                    $range = 25;

                    //  $savedLat =    40.8825278;
                    //  $savedLng =  17.1720556;

                    //  $savedLat =    40.8823762;
                    //  $savedLng =  17.1718139;



                    foreach ($positionsData['data'] as $position) {
                        if (isset($position['lat']) && isset($position['lng']) && isset($position['date'])) {
                            $lat = $position['lat'];
                            $lng = $position['lng'];
                            $date = $position['date'];

                            $ConstructionLocations  = ConstructionLocation::get();
                            foreach ($ConstructionLocations as $ConstructionLocation) {
                                $savedLat =  $ConstructionLocation->latitude;
                                $savedLng =  $ConstructionLocation->langitude;

                                $distance = haversine($savedLat, $savedLng, $position['lat'], $position['lng']);

                                if ($distance <= $range) {
                                    $matchingLocations[] = [
                                        'construction_site_id' => $ConstructionLocation->construction_site_id,
                                        'latitude' => $ConstructionLocation->latitude,
                                        'longitude' => $ConstructionLocation->langitude,
                                        'date' => $date
                                    ];
                                }
                            }
                        }
                    }
                      $constructionSiteIds = array_column($matchingLocations, 'construction_site_id');
                      
               
                      
                      $uniqueConstructionSiteIds = array_unique($constructionSiteIds);
                      
                
                      $uniqueMatchingLocations = [];
                      
                      
                      foreach ($matchingLocations as $location) {
                        if (in_array($location['construction_site_id'], $uniqueConstructionSiteIds)) {
                            $uniqueMatchingLocations[] = $location;
                            // Remove the construction_site_id from the list to avoid duplicates
                            unset($uniqueConstructionSiteIds[array_search($location['construction_site_id'], $uniqueConstructionSiteIds)]);
                        }
                    }
                    
                    // Now $uniqueMatchingLocations contains only unique entries based on construction_site_id
                    // dd($uniqueMatchingLocations);
                                          
                      
                      
                    // Remove duplicates from the $matchingLocations array
                    // $matchingLocations = array_unique($matchingLocations, SORT_REGULAR);

                    // dd($matchingLocations);
                    foreach ($uniqueMatchingLocations as $matchingLocation) {
                        ConstructionMaterial::updateOrCreate(
                            [
                                'construction_site_id' => $matchingLocation['construction_site_id'],
                                'material_list_id' => 295,
                            ],
                            [
                                'updated_at' => $matchingLocation['date'],
                            ],
                            [
                                'timestamps' => false,
                            ]
                        );
                    }

                    // return response()->json(['message' => 'Data fetched successfully']);
                } else {
                    // Handle the error when fetching positions
                    // return response()->json(['error' => 'Error fetching positions'], $positionsResponse->getStatusCode());
                }
            } else {
                // Handle the error when logging in
                // return response()->json(['error' => 'Error logging in'], $loginResponse->getStatusCode());
            }
        } catch (\Exception $e) {
            // dd($e);
            // Handle other exceptions
            // return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }



    public function saveNewCantiereCoordinates()
    {
        $ConstructionLocation = ConstructionLocation::pluck('construction_site_id')->toarray();

        ConstructionSite::whereNotIn('id', $ConstructionLocation)->where('page_status', 4)->chunk(100, function ($constructionSites) {
        // ConstructionSite::where('pin_location', '!=', null)->chunk(100, function ($constructionSites) {

            foreach ($constructionSites as $data) {
                $id = $data->id;

                $address = null;
                if ($data->PropertyData->property_street != null || $data->PropertyData->property_house_number != null  || $data->PropertyData->property_common != null) {
                    $address = $data->PropertyData->property_street . '+' . $data->PropertyData->property_house_number . '+' . $data->PropertyData->property_common;
                }

                // if ($data->pin_location != null) {
                        
                

                // } elseif ($address != null) {
                if ($address != null)
                {
                    try {

                        $apiKey = 'AIzaSyBJx5-Ibg8Crb8yWXfYW1ssOccCbQa4PJo';
                        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                            'address' => $address,
                            'key' => $apiKey,
                        ]);
                        $data = $response->json();

                        if (isset($data['results'][0]['geometry']['location'])) {

                            $location = $data['results'][0]['geometry']['location'];


                            $radius = 0.000225; // 25 meters in degrees

                            $latitude = $location['lat'];
                            $longitude = $location['lng'];

                            ConstructionLocation::updateOrCreate(
                                ['construction_site_id' => $id],
                                ['latitude' => $latitude, 'langitude' => $longitude]
                            );
                        }
                    } catch (\Exception $e) {
                        echo "Geocoding failed for Id:" . $id . " " . $e->getMessage() . "\n";
                    }
                }
            }
        });
    }
}
