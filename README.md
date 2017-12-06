# CakePHP Geocoder Plugin
## A thin wrapper around the Google Maps Geocoding API.

This plugin adds a geocoder controller component and model behavior to your CakePHP 3 application.
It is based on Martin Bean's [CakePHP 2 Geocoder Plugin](https://github.com/martinbean/cakephp-geocoding-plugin), updated for compatibility with CakePHP 3.
Thanks to Martin for making his code available under a MIT licence.

## Installation

Install from [packagist](https://packagist.org/packages/chris48s/cakephp-geocoder) using [composer](https://getcomposer.org/).
Add the following to your `composer.json`:

```
"require": {
    "chris48s/cakephp-geocoder": "^1.0.0"
}
```

and run `composer install` or `composer update`, as applicable.

## Loading the plugin

Add the code `Plugin::load('Chris48s/Geocoder');` to your `bootstrap.php`.

## Using the Component

You can use the component to geocode addresses within your controllers. A good example is if you need to take a user-submitted value and convert it to a latitude/longitude pair to pass to a model to search it.

To geocode an address in your controllers, do something similar to the following:

```php
<?php
namespace App\Controller;

use App\Controller\AppController;

class StoresController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Chris48s/Geocoder.Geocoder');
    }

    public function search()
    {

        $location = $this->request->query['location'];

        $geocodeResult = $this->Geocoder->geocode($location);

        if (count($geocodeResult) > 0) {
            $latitude = floatval($geocodeResult[0]->geometry->location->lat);
            $longitude = floatval($geocodeResult[0]->geometry->location->lng);
        }
    }
}
```

The component will return a response as a native PHP object from Google’s Geocoding API.

## Using the Behavior

There is also a model behavior. This is useful if saving a record and you want to create a latitude/longitude pair from a field in your model’s data that represents the address, for example a store. Attach the behavior in your table class:

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class Stores extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Chris48s/Geocoder.Geocodable');
    }
}
```

### Configuration

By default, the behavior assumes you have two columns in your corresponding database table called `latitude` and `longitude`, and also a column called `address`. These can be changed though. Simply pass an array of options when attaching the behavior:

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class Stores extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Chris48s/Geocoder.Geocodable', [
            'addressColumn' => 'street_address',
            'latitudeColumn' => 'lat',
            'longitudeColumn' => 'lng'
        ]);
    }
}
```

The `addressColumn` key also accepts an array. If you pass an array for the value, then the behavior will iterate over the fields and assemble the address that way. So if you store addresses as their separate components then you could do the following:

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class Stores extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Chris48s/Geocoder.Geocodable', [
            'addressColumn' => [
                'street_address',
                'locality',
                'postal_code'
            ]
        ]);
    }
}
```

If `addressColumn` is not a string or an array, the Behavior will throw an exception of class `GeocoderException`.

By default, the behavior will not save the address if geocoding fails. This can also be configured using the `requireSuccess` key. Set this to false to save the address anyway even if geocoding fails:

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class Stores extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Chris48s/Geocoder.Geocodable', [
            'requireSuccess' => false,
        ]);
    }
}
```

If using this setting, your `latitudeColumn` and `longitudeColumn` should be set to allow NULL.

## API Keys

To use the plugin with a Google API key, [obtain a key](https://developers.google.com/maps/documentation/geocoding/get-api-key) and pass it in when calling the component

```php
$this->loadComponent('Chris48s/Geocoder.Geocoder');
$geocodeResult = $this->Geocoder->geocode($location, ['key' => 'my-api-key']);
```

or when configuring the behaviour

```php
$this->addBehavior('Chris48s/Geocoder.Geocodable', [
    'parameters' => ['key' => 'my-api-key']
]);
```

## Error Handling

If the Google Maps Geocoding API returns a status code other than 200 OK, the Component will throw an exception of class `GeocoderException`.
The Behavior will add a validation error to the target entity object which can be accessed using `$entity->errors()`.

## Reporting Issues

If you have any issues with this plugin then please feel free to create a new [Issue](https://github.com/chris48s/cakephp-geocoder/issues) on the [GitHub repository](https://github.com/chris48s/cakephp-geocoder). This plugin is licensed under the MIT License.
