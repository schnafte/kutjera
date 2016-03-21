<?php
namespace Schnafte\Kutjera\DataProvider;

use MicroDB\Database;

class MicroDB implements DataProviderInterface
{
    /**
     * @var array
     */
    private $resourceToPathMappings;


    /**
     * MicroDB constructor.
     * @param array $resourceToPathMappings
     */
    public function __construct(array $resourceToPathMappings)
    {
        foreach ($resourceToPathMappings as $mapping) {
            $this->resourceToPathMappings[$mapping['name']] = $mapping['path'];
        }
    }


    /**
     * @param string $resource
     * @param $id
     * @return array
     */
    public function getResource($resource, $id)
    {
        $db = $this->getDBForResource($resource);

        $rs = $db->load([$id]);

        return $rs;
    }

    /**
     * @param string $resource
     * @return array
     */
    public function getCollection($resource)
    {
        $db = $this->getDBForResource($resource);

        $rs = $db->find();

        return $rs;
    }


    /**
     * @param $resource
     * @return Database
     * @throws \Exception
     */
    protected function getDBForResource($resource)
    {
        if (!isset($this->resourceToPathMappings[$resource])) {
            throw new \Exception(sprintf("Cannot create MicroDB Instance. No path mapping configured for this resource: %s", $resource));
        }

        return new Database($this->resourceToPathMappings[$resource]);
    }
}