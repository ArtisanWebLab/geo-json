# GeoJson PHP Library

This library implements the
[GeoJSON format specification](http://www.geojson.org/geojson-spec.html).

The `GeoJson` namespace includes classes for each data structure defined in the GeoJSON specification. Core GeoJSON objects include geometries, features, and collections. Geometries range from primitive points to more complex polygons. Classes also exist for bounding boxes and coordinate reference systems.

## Installation

```
$ composer require "artisanweblab/geojson"
```

## Usage

Classes in this library are immutable.

### GeoJson Constructors

Geometry objects are constructed using a single coordinates array. This may be a tuple in the case of a `Point`, an array of tuples for a `LineString`, etc. Constructors for each class will validate the coordinates array and throw an
`InvalidArgumentException` on error.

More primitive geometry objects may also be used for constructing complex objects. For instance, a `LineString` may be constructed from an array of
`Point` objects.

Feature objects are constructed from a geometry object, associative properties array, and an identifier, all of which are optional.

Feature and geometry collection objects are constructed from an array of their respective types.

### JSON Serialization

```php
use ArtisanWebLab\GeoJson\Geometry\Point;
$point = Point::latLng([50.4019514, 30.3926095]);
$json = json_encode($point);
```

Printing the `$json` variable would yield (sans whitespace):

```json
{
    "type": "Point",
    "coordinates": [30.3926095, 50.4019514]
}
```

### JSON Unserialization

The core `GeoJson` class implements an internal `JsonUnserializable` interface, which defines a static factory method, `jsonUnserialize()`, that can be used to create objects from the return value of `json_decode()`.

```php
use ArtisanWebLab\GeoJson\GeoJson;
$json = '{ "type": "Point", "coordinates": [30.3926095, 50.4019514] }';
$point = GeoJson::jsonUnserialize($json);
```

If errors are encountered during unserialization, an `UnserializationException`
will be thrown by `jsonUnserialize()`. Possible errors include:

* Missing properties (e.g. `type` is not present)
* Unexpected values (e.g. `coordinates` property is not an array)
* Unsupported `type` string when parsing a GeoJson object or CRS

### Polyline Decoder

```php
$origin = 'Маріїнський палац, 5A, вулиця Михайла Грушевського, Київ, 01008';
$destination = 'Києво-Печерська лавра, вулиця Лаврська, 15, Київ, 01015';

$request = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
    'origin'      => $origin,
    'destination' => $destination,
    'language'    => 'ua',
    'key'         => '...',
]);
$response = $request->json();

$lineStrings = [];

foreach ($response['routes'] as $route) {
    $polylineFromGoogleMapsAPI = $route['overview_polyline']['points'];
    $lineStrings[] = GeoJson::decodePolyline($polylineFromGoogleMapsAPI);
}
```

```json
{
    "type":"LineString",
    "coordinates":[
        [30.5385,50.4478],
        [30.53711,50.44677],
        //..
        [30.55699,50.43555],
        [30.55701,50.43553]
    ]
}
```
