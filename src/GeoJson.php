<?php

namespace ArtisanWebLab\GeoJson;

use ArrayObject;
use ArtisanWebLab\GeoJson\Exception\UnserializationException;
use ArtisanWebLab\GeoJson\Geometry\LineString;
use ArtisanWebLab\GeoJson\Geometry\Point;
use JsonSerializable;
use ReflectionClass;

/**
 * Base GeoJson object.
 *
 * @see   http://www.geojson.org/geojson-spec.html#geojson-objects
 * @since 1.0
 */
abstract class GeoJson implements JsonSerializable, JsonUnserializable
{
    protected string $type;

    /**
     * @see JsonUnserializable::jsonUnserialize()
     */
    final public static function jsonUnserialize($json)
    {
        if (empty($json)) {
            return null;
        }

        if (is_string($json)) {
            $json = json_decode($json);
        }

        if (!is_array($json) && !is_object($json)) {
            throw UnserializationException::invalidValue('GeoJson', $json, 'array or object');
        }

        $json = new ArrayObject($json);

        if (!$json->offsetExists('type')) {
            throw UnserializationException::missingProperty('GeoJson', 'type', 'string');
        }

        $type = (string)$json['type'];
        $args = [];

        switch ($type) {
            case 'LineString':
            case 'MultiLineString':
            case 'MultiPoint':
            case 'MultiPolygon':
            case 'Point':
            case 'Polygon':
                if (!$json->offsetExists('coordinates')) {
                    throw UnserializationException::missingProperty($type, 'coordinates', 'array');
                }

                if (!is_array($json['coordinates'])) {
                    throw UnserializationException::invalidProperty($type, 'coordinates', $json['coordinates'], 'array');
                }

                $args[] = $json['coordinates'];
                break;

            case 'Feature':
                $geometry = isset($json['geometry']) ? $json['geometry'] : null;
                $properties = isset($json['properties']) ? $json['properties'] : null;

                if (isset($geometry) && !is_array($geometry) && !is_object($geometry)) {
                    throw UnserializationException::invalidProperty($type, 'geometry', $geometry, 'array or object');
                }

                if (isset($properties) && !is_array($properties) && !is_object($properties)) {
                    throw UnserializationException::invalidProperty($type, 'properties', $properties, 'array or object');
                }

                $args[] = isset($geometry) ? self::jsonUnserialize($geometry) : null;
                $args[] = isset($properties) ? (array)$properties : null;
                $args[] = isset($json['id']) ? $json['id'] : null;
                break;

            case 'FeatureCollection':
                if (!$json->offsetExists('features')) {
                    throw UnserializationException::missingProperty($type, 'features', 'array');
                }

                if (!is_array($json['features'])) {
                    throw UnserializationException::invalidProperty($type, 'features', $json['features'], 'array');
                }

                $args[] = array_map(['self', 'jsonUnserialize'], $json['features']);
                break;

            case 'GeometryCollection':
                if (!$json->offsetExists('geometries')) {
                    throw UnserializationException::missingProperty($type, 'geometries', 'array');
                }

                if (!is_array($json['geometries'])) {
                    throw UnserializationException::invalidProperty($type, 'geometries', $json['geometries'], 'array');
                }

                $args[] = array_map(['self', 'jsonUnserialize'], $json['geometries']);
                break;

            default:
                throw UnserializationException::unsupportedType('GeoJson', $type);
        }

        $class = sprintf('ArtisanWebLab\GeoJson\%s\%s', (strncmp('Feature', $type, 7) === 0 ? 'Feature' : 'Geometry'), $type);
        $class = new ReflectionClass($class);

        return $class->newInstanceArgs($args);
    }

    final public static function decodePolyline(string $value): LineString
    {
        $index = 0;
        $lat = 0;
        $lng = 0;
        $points = [];

        while ($index < strlen($value)) {
            $latResult = 0;
            $lngResult = 0;

            $shift = 0;
            do {
                $byte = ord(substr($value, $index++, 1)) - 63;
                $latResult |= ($byte & 0x1f) << $shift;
                $shift += 5;
            } while ($byte > 31);

            $shift = 0;
            do {
                $byte = ord(substr($value, $index++, 1)) - 63;
                $lngResult |= ($byte & 0x1f) << $shift;
                $shift += 5;
            } while ($byte > 31);

            $lat += (($latResult & 1) ? ~($latResult >> 1) : ($latResult >> 1));
            $lng += (($lngResult & 1) ? ~($lngResult >> 1) : ($lngResult >> 1));

            $points[] = new Point([$lng / 100000, $lat / 100000]);
        }

        return new LineString($points);
    }

    /**
     * Return the type for this GeoJson object.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        return ['type' => $this->type];
    }
}
