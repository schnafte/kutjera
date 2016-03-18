<?php
namespace Kutjera;


/**
 * Class QueryStringParser
 * @package Kutjera
 */
class QueryStringParser
{
    /**
     * @var string
     */
    private $queryString;

    /**
     * @var string
     */
    private $filtersKey;

    /**
     * @var string
     */
    private $sortingKey;


    /**
     * @param string $queryString
     * @param string $filtersKey
     * @param string $sortingKey
     */
    public function __construct($queryString = '', $filtersKey = 'filter', $sortingKey = 'sort')
    {
        $this->validateQueryString($queryString);
        $this->validateFiltersKey($filtersKey);
        $this->validateSortingKey($sortingKey);

        $this->queryString = trim($queryString);
        $this->filtersKey = trim($filtersKey);
        $this->sortingKey = trim($sortingKey);
    }


    /*******************************************************************************************************************
     * PUBLIC
     */

    public function parse()
    {
        $filters = [];
        $sorting = [];
        $preParsed = [];

        parse_str($this->queryString, $preParsed);

        if (isset($preParsed[$this->filtersKey])) {
            $filters = $this->parseFiltersString($preParsed[$this->filtersKey]);
        }

        if (isset($preParsed[$this->sortingKey])) {
            $sorting = $this->parseSortingString($preParsed[$this->sortingKey]);
        }

        return new DataQuery($filters, $sorting);
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * @param string $queryString
     * @return $this
     */
    public function setQueryString($queryString)
    {
        $this->validateQueryString($queryString);

        $this->queryString = trim($queryString);

        return $this;
    }

    /**
     * @return string
     */
    public function getFiltersKey()
    {
        return $this->filtersKey;
    }

    /**
     * @param string $filtersKey
     * @return $this
     */
    public function setFiltersKey($filtersKey)
    {
        $this->validateFiltersKey($filtersKey);

        $this->filtersKey = trim($filtersKey);
        return $this;
    }

    /**
     * @return string
     */
    public function getSortingKey()
    {
        return $this->sortingKey;
    }

    /**
     * @param string $sortingKey
     * @return $this
     */
    public function setSortingKey($sortingKey)
    {
        $this->validateSortingKey($sortingKey);

        $this->sortingKey = trim($sortingKey);
        return $this;
    }



    /*******************************************************************************************************************
     * INTERNAL
     */

    /**
     * @param $queryString
     * @return FilterRule[]
     */
    protected function parseFiltersString($queryString)
    {
        $parts = explode(',', $queryString);
        $filters = [];

        foreach ($parts as $part) {

            $splitFieldAndRest = explode('=', $part);

            if (sizeof($splitFieldAndRest) !== 2) {
                throw new \InvalidArgumentException(sprintf("Invalid filter query string <%s>. Expected format is: <field>=[<operator>]<value>", $part));
            }

            $field = $splitFieldAndRest[0];

            $splitOperatorAndValue = explode(':', $splitFieldAndRest[1]);

            if (sizeof($splitOperatorAndValue) === 1) {
                $op = FilterRule::OP_EQ;
                $value = $splitOperatorAndValue[0];
            } else {
                $op = $splitOperatorAndValue[0];
                $value = $splitOperatorAndValue[1];

                $this->validateOperator($op);
            }

            $this->validateValue($value, $field, $op);

            $rule = new FilterRule($field, $op, $value);

            $filters[]= $rule;
        }

        return $filters;
    }

    /**
     * @param $queryString
     * @return SortingRule[]
     */
    protected function parseSortingString($queryString)
    {
        $parts = explode(',', $queryString);
        $sorting = [];

        foreach ($parts as $part) {

            if ($part[0] == '-') {
                $order = SortingRule::ORDER_DESC;
                $field = ltrim($part, '-');
            } else {
                $order = SortingRule::ORDER_ASC;
                $field = $part;
            }

            $rule = new SortingRule($field, $order);

            $sorting[]= $rule;
        }

        return $sorting;
    }


    /**
     * @param $op
     * @return bool
     */
    protected function validateOperator($op)
    {
        if (!in_array($op, [
            FilterRule::OP_EQ,
            FilterRule::OP_NEQ,
            FilterRule::OP_GT,
            FilterRule::OP_GTE,
            FilterRule::OP_LT,
            FilterRule::OP_LTE,
            FilterRule::OP_ANY,
            FilterRule::OP_CTS,
            FilterRule::OP_LKE
        ])) {
            throw new \InvalidArgumentException(sprintf("Invalid operator %s", $op));
        }

        return true;
    }


    /**
     * @param $queryString
     * @return bool
     */
    protected function validateQueryString($queryString)
    {
        if (!is_string($queryString) || is_numeric($queryString) || is_null($queryString) ) {
            throw new \InvalidArgumentException('The $queryString parameter has to be of type string.');
        }

        return true;
    }


    /**
     * @param $filtersKey
     * @return bool
     */
    protected function validateFiltersKey($filtersKey)
    {
        if (!is_string($filtersKey) || is_numeric($filtersKey) || is_null($filtersKey) || empty(trim($filtersKey))) {
            throw new \InvalidArgumentException('The $filtersKey parameter has to be of type string and must not be empty.');
        }

        return true;
    }


    /**
     * @param $sortingKey
     * @return bool
     */
    protected function validateSortingKey($sortingKey)
    {
        if (!is_string($sortingKey) || is_numeric($sortingKey) || is_null($sortingKey) || empty(($sortingKey)) ) {
            throw new \InvalidArgumentException('The $sortingKey parameter has to be of type string and must not be empty');
        }

        return true;
    }


    /**
     * @param $value
     * @param string $field
     * @param string $operator
     * @return bool
     */
    protected function validateValue($value, $field, $operator)
    {
        if (empty($value) ) {
            throw new \InvalidArgumentException(sprintf('The filter value must not be empty.(field: %s)', $field));
        }

        return true;
    }
}