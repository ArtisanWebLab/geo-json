<?php

namespace ArtisanWebLab\GeoJson\Geometry;

/**
 * MultiLineString geometry object.
 * Coordinates consist of an array of LineString coordinates.
 *
 * @see   http://www.geojson.org/geojson-spec.html#multilinestring
 * @since 1.0
 */
class MultiLineString extends Geometry
{
    protected string $type = 'MultiLineString';

    /**
     * MultiLineString constructor.
     *
     * @param  array  $lineStrings
     */
    public function __construct(array $lineStrings)
    {
        $this->coordinates = array_map(function ($lineString) {
            if (!$lineString instanceof LineString) {
                $lineString = new LineString($lineString);
            }

            return $lineString->getCoordinates();
        }, $lineStrings);
    }
}
