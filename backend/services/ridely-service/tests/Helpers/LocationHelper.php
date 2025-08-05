<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LocationHelper
{
    public static string $pickUp = "Avenida Beira Mar, 25";
    public static string $dropOff = "Avenida Euclides Figueiredo, 65";


    public static function getDatasourceDataForPickUpSuccessResponse()
    {
        // https://nominatim.openstreetmap.org/search?q=Avenida+Beira+Mar%2C+25+Aracaju+SE&format=jsonv2
        $pickUpResponse = File::get(base_path('tests/Datasources/http/nominatim/search-Avenida+Beira+Mar+25+Aracaju+SE.json'));
        return json_decode($pickUpResponse, true);
    }

    public static function getDatasourceDataForDropOffSuccessResponse()
    {
        // https://nominatim.openstreetmap.org/search?q=Avenida+Euclides+Figueiredo+65+Aracaju+SE&format=jsonv2
        $dropOffResponse = File::get(base_path('tests/Datasources/http/nominatim/search-Avenida+Euclides+Figueiredo+65+Aracaju+SE.json'));
        return json_decode($dropOffResponse, true);
    }

    public static function mockCall($locationServiceUrl, $address, $result, $statusCode = 200)
    {
        $q = rawurlencode($address);
        $url = "$locationServiceUrl?format=jsonv2&q=$q*";
        Http::fake([
            $url => Http::response($result, $statusCode),
        ]);
    }
}