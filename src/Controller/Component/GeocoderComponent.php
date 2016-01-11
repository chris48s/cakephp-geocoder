<?php

namespace chris48s\Geocoder\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

class GeocoderComponent extends Component
{
    /**
     * Geocodes an address.
     *
     * @param string $address
     * @param array $parameters
     * @return object
     * @todo Determine what to do if response status is an error
     */
    public function geocode($address, $parameters = [])
    {
        $parameters['address'] = $address;
        $parameters['sensor'] = 'false';

        $url = 'http://maps.googleapis.com/maps/api/geocode/json';

        $http = new Client();

        $response = $http->get($url, $parameters);
        $response = json_decode($response->body());

        return $response->results;
    }
}
