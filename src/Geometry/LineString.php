<?php

namespace ArtisanWebLab\GeoJson\Geometry;

use InvalidArgumentException;

/**
 * LineString geometry object.
 * Coordinates consist of an array of at least two positions.
 *
 * @see   http://www.geojson.org/geojson-spec.html#linestring
 * @since 1.0
 */
class LineString extends MultiPoint
{
    protected string $type = 'LineString';

    /**
     * LineString constructor.
     *
     * @param array $positions
     */
    public function __construct(array $positions)
    {
        if (count($positions) < 2) {
            throw new InvalidArgumentException('LineString requires at least two positions');
        }

        parent::__construct($positions);
    }
}
