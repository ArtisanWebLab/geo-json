<?php

namespace ArtisanWebLab\GeoJson\Geometry;

/**
 * MultiPoint geometry object.
 * Coordinates consist of an array of positions.
 *
 * @see   http://www.geojson.org/geojson-spec.html#multipoint
 * @since 1.0
 */
class MultiPoint extends Geometry
{
    protected string $type = 'MultiPoint';

    /**
     * MultiPoint constructor.
     *
     * @param  array  $positions
     */
    public function __construct(array $positions)
    {
        $this->coordinates = array_map(function ($point) {
            if (!$point instanceof Point) {
                $point = new Point($point);
            }

            return $point->getCoordinates();
        }, $positions);
    }
}
