<?php

namespace ArtisanWebLab\GeoJson\Geometry;

/**
 * Polygon geometry object.
 * Coordinates consist of an array of LinearRing coordinates.
 *
 * @see   http://www.geojson.org/geojson-spec.html#polygon
 * @since 1.0
 */
class Polygon extends Geometry
{
    protected string $type = 'Polygon';

    /**
     * Polygon constructor.
     *
     * @param array $linearRings
     */
    public function __construct(array $linearRings)
    {
        foreach ($linearRings as $linearRing) {
            if (!$linearRing instanceof LinearRing) {
                $linearRing = new LinearRing($linearRing);
            }

            $this->coordinates[] = $linearRing->getCoordinates();
        }
    }
}
