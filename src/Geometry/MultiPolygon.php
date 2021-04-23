<?php

namespace ArtisanWebLab\GeoJson\Geometry;

/**
 * MultiPolygon geometry object.
 * Coordinates consist of an array of Polygon coordinates.
 *
 * @see   http://www.geojson.org/geojson-spec.html#multipolygon
 * @since 1.0
 */
class MultiPolygon extends Geometry
{
    protected string $type = 'MultiPolygon';

    /**
     * MultiPolygon constructor.
     *
     * @param  array  $polygons
     */
    public function __construct(array $polygons)
    {
        $this->coordinates = array_map(function ($polygon) {
            if (!$polygon instanceof Polygon) {
                $polygon = new Polygon($polygon);
            }

            return $polygon->getCoordinates();
        }, $polygons);
    }
}
