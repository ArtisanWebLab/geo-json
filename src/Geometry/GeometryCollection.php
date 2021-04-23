<?php

namespace ArtisanWebLab\GeoJson\Geometry;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Collection of Geometry objects.
 *
 * @see   http://www.geojson.org/geojson-spec.html#geometry-collection
 * @since 1.0
 */
class GeometryCollection extends Geometry implements Countable, IteratorAggregate
{
    protected string $type = 'GeometryCollection';

    protected array $geometries;

    /**
     * GeometryCollection constructor.
     *
     * @param  array  $geometries
     */
    public function __construct(array $geometries)
    {
        foreach ($geometries as $geometry) {
            if (!$geometry instanceof Geometry) {
                throw new InvalidArgumentException('GeometryCollection may only contain Geometry objects');
            }
        }

        $this->geometries = array_values($geometries);
    }

    /**
     * @see http://php.net/manual/en/countable.count.php
     */
    public function count(): int
    {
        return count($this->geometries);
    }

    /**
     * Return the Geometry objects in this collection.
     *
     * @return Geometry[]
     */
    public function getGeometries(): array
    {
        return $this->geometries;
    }

    /**
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->geometries);
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'geometries' => array_map(
                function (Geometry $geometry) {
                    return $geometry->jsonSerialize();
                },
                $this->geometries
            ),
        ]);
    }
}
