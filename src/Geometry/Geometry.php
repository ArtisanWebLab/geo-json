<?php

namespace ArtisanWebLab\GeoJson\Geometry;

use ArtisanWebLab\GeoJson\GeoJson;

/**
 * Base geometry object.
 *
 * @see   http://www.geojson.org/geojson-spec.html#geometry-objects
 * @since 1.0
 */
abstract class Geometry extends GeoJson
{
    protected array $coordinates;

    /**
     * Return the coordinates for this Geometry object.
     *
     * @return array
     */
    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();

        if (isset($this->coordinates)) {
            $json['coordinates'] = $this->coordinates;
        }

        return $json;
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}
