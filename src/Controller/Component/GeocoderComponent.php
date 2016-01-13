<?php

namespace chris48s\Geocoder\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;
use chris48s\Geocoder\Exception\GeocoderException;

class GeocoderComponent extends Component
{
    /**
     * Geocodes an address.
     *
     * @param string $address
     * @param array $parameters
     * @throws GeocoderException if the API return a status code other than 200
     * @return object
     */
    public function geocode($address, $parameters = [])
    {
        $parameters['address'] = $address;
        $parameters['sensor'] = 'false';

        $url = 'http://maps.googleapis.com/maps/api/geocode/json';

        $http = new Client();

        $response = $http->get($url, $parameters);
        if ($response->status != 'OK') {
            throw new GeocoderException('Google Maps Geocoding API returned status code ' . $response->code);
        }
        $response = json_decode($response->body());

        return $response->results;
    }
}
