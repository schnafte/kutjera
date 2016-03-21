<?php
namespace Kutjera;

use Kutjera\DataProvider\DataProviderInterface;
use Kutjera\Util\ArrayHelper;

/**
 *
 */
class Repository
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;


    /**
     * Repository constructor.
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }


    /**
     * @param string $resource
     * @param DataQueryInterface $query
     * @return array
     */
    public function query($resource, DataQueryInterface $query)
    {
        $rs = $this->dataProvider->getCollection($resource);

        return $this->processResult($rs, $query);
    }


    /**
     * @param $resource
     * @param $id
     * @return array
     */
    public function byId($resource, $id)
    {
        $rs = $this->dataProvider->getResource($resource, $id);

        return $rs;
    }


    /**
     * @param array $rs
     * @param DataQueryInterface $query
     * @return array
     */
    protected function processResult(array $rs, DataQueryInterface $query)
    {
        if (sizeof($query->getFilters()) > 0) {
            $rs = $this->filter($rs, $query->getFilters());
        }

        if (sizeof($query->getSorting()) > 0) {
            $rs = $this->sort($rs, $query->getSorting());
        }

        if ($query->getLimit()->isDefined()) {
            $rs = $this->limit($rs, $query->getLimit());
        }

        if (sizeof($query->getFields()) > 0) {
            $rs = $this->extractFields($rs, $query->getFields());
        }

        return $rs;
    }


    /**
     * @param array $rs
     * @param FilterRule[] $filters
     * @return array
     */
    protected function filter(array $rs, array $filters)
    {
        $rs = array_filter($rs, function($row) use($filters) {
            $include = true;

            foreach ($filters as $filter) {
                $value = ArrayHelper::get($row, $filter->getField());
                $include = self::compare($value, $filter->getOperator(), $filter->getValue());

                if ($include == false) {
                    break;
                }
            }

            return $include;
        });

        return $rs;
    }

    /**
     * @param array $rs
     * @param SortingRule[] $sorting
     * @return array
     */
    protected function sort(array $rs, array $sorting)
    {
        foreach ($sorting as $sortRule) {
            usort($rs, function($a, $b) use($sortRule) {
                $aValue = ArrayHelper::get($a, $sortRule->getField());
                $bValue = ArrayHelper::get($b, $sortRule->getField());

                return $sortRule->getOrder() == SortingRule::ORDER_ASC ? strnatcasecmp($aValue, $bValue) : strnatcasecmp($bValue, $aValue);
            });
        }

        return $rs;
    }


    /**
     * @param array $rs
     * @param LimitRule $limit
     * @return array
     */
    protected function limit(array $rs, LimitRule $limit)
    {
        return array_slice($rs,$limit->getOffset(), $limit->getCount());
    }


    /**
     * @param array $rs
     * @param array $fields
     * @return array
     */
    protected function extractFields(array $rs, array $fields)
    {
        return array_map(function($el) use ($fields) {
            $mapped = [];

            foreach($fields as $field) {
                $mapped = ArrayHelper::add($mapped, $field, ArrayHelper::get($el, $field));
            }

            return $mapped;
        }, $rs);
    }



    protected function compare($value, $op, $input)
    {
        $valueIsNumeric = is_numeric($value);
        $valueIsString = is_string($value);
        $valueIsArray = is_array($value);

        $matches = false;

        switch ($op) {
            case 'eq':
                if ($valueIsNumeric) {
                    $matches = $value == $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) == 0;

                }

                break;

            case 'gt':
                if ($valueIsNumeric) {
                    $matches = $value > $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) > 0;

                }

                break;

            case 'gte':

                if ($valueIsNumeric) {
                    $matches = $value >= $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) >= 0;

                }

                break;

            case 'lt':

                if ($valueIsNumeric) {
                    $matches = $value < $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) > 0;

                }

                break;

            case 'lte':

                if ($valueIsNumeric) {
                    $matches = $value <= $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) >= 0;

                }

                break;

            case 'neq':

                if ($valueIsNumeric) {
                    $matches = $value != $input;

                } else if ($valueIsString) {
                    $matches = strnatcasecmp($input, $value) != 0;

                }

                break;

            case 'any':

                if ($valueIsArray) {
                    $matches = in_array($value, $input);
                }

                break;

            case 'cts':

                if ($valueIsArray) {
                    $matches = in_array($input, $value);
                }

                break;

            case 'like':

                if ($valueIsString) {
                    $matches = stripos($value, $input) !== false;

                }

                break;

            default:
                throw new \InvalidArgumentException(sprintf("Invalid operator '%s'", $op));
                break;
        }

        return $matches;
    }
}