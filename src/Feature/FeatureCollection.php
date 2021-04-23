<?php

namespace ArtisanWebLab\GeoJson\Feature;

use ArrayIterator;
use Countable;
use ArtisanWebLab\GeoJson\GeoJson;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Collection of Feature objects.
 *
 * @see   http://www.geojson.org/geojson-spec.html#feature-collection-objects
 * @since 1.0
 */
class FeatureCollection extends GeoJson implements Countable, IteratorAggregate
{
    protected string $type = 'FeatureCollection';

    /**
     * @var array
     */
    protected $features;

    /**
     * FeatureCollection constructor.
     *
     * @param array $features
     */
    public function __construct(array $features)
    {
        foreach ($features as $feature) {
            if (!$feature instanceof Feature) {
                throw new InvalidArgumentException('FeatureCollection may only contain Feature objects');
            }
        }

        $this->features = array_values($features);
    }

    /**
     * @see http://php.net/manual/en/countable.count.php
     */
    public function count()
    {
        return count($this->features);
    }

    /**
     * Return the Feature objects in this collection.
     *
     * @return Feature[]
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator()
    {
        return new ArrayIterator($this->features);
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'features' => array_map(
                    function (Feature $feature) {
                        return $feature->jsonSerialize();
                    },
                    $this->features
                ),
            ]
        );
    }
}
