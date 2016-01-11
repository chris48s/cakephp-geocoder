<?php

namespace chris48s\Geocoder\Model\Behavior;

use Cake\Event\Event;
use Cake\Network\Http\Client;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;

class GeocodableBehavior extends Behavior
{
    protected $_defaultConfig = [
        'addressColumn' => 'address',
        'latitudeColumn' => 'latitude',
        'longitudeColumn' => 'longitude',
        'parameters' => []
    ];

    /**
     * Before save callback.
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be saved
     * @return boolean
     * @todo Throw exception or something if address column is not either an array or a string
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        $addressColumn = $this->_config['addressColumn'];
        $latitudeColumn = $this->_config['latitudeColumn'];
        $longitudeColumn = $this->_config['longitudeColumn'];
        $parameters = (array) $this->_config['parameters'];

        if (is_array($addressColumn)) {
            $address = [];
            foreach ($addressColumn as $column) {
                if (!empty($entity->{$column})) {
                    $address[] = $entity->{$column};
                }
            }
            $address = implode(', ', $address);
        } else {
            $address = $entity->{$addressColumn};
        }

        $parameters['address'] = $address;
        $parameters['sensor'] = 'false';

        $url = 'http://maps.googleapis.com/maps/api/geocode/json';

        $http = new Client();

        $response = $http->get($url, $parameters);
        $response = json_decode($response->body());

        if ($response->status == 'OK') {
            $entity->{$latitudeColumn} = floatval($response->results[0]->geometry->location->lat);
            $entity->{$longitudeColumn} = floatval($response->results[0]->geometry->location->lng);
            return true;
        } else {
            $entity->errors($addressColumn, 'Could not geocode address');
            return false;
        }
    }
}
