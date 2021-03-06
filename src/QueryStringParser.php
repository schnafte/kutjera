<?php
namespace Schnafte\Kutjera;


/**
 * Class QueryStringParser
 * @package Kutjera
 */
class QueryStringParser
{
    /**
     * @param string|null $queryString
     * @param string $filtersKey
     * @param string $sortingKey
     * @param string $fieldsKey
     * @param string $limitKey
     * @return DataQuery
     */
    public function parse($queryString, $filtersKey = 'filter', $sortingKey = 'sort',  $fieldsKey = 'fields', $limitKey = 'limit')
    {
        $filters = [];
        $sorting = [];
        $fields = [];
        $limit = new LimitRule();

        if ($queryString == null) {
            return new DataQuery($queryString, $filters, $sorting, $fields, $limit);
        }

        $this->validateQueryString($queryString);
        $this->validateFiltersKey($filtersKey);
        $this->validateSortingKey($sortingKey);
        $this->validateFieldsKey($fieldsKey);
        $this->validateLimitKey($limitKey);

        $queryString = trim($queryString);
        $filtersKey = trim($filtersKey);
        $sortingKey = trim($sortingKey);
        $fieldsKey = trim($fieldsKey);
        $limitKey = trim($limitKey);


        $preParsed = [];

        parse_str($queryString, $preParsed);

        if (isset($preParsed[$filtersKey])) {
            $filters = $this->parseFiltersString($preParsed[$filtersKey]);
        }

        if (isset($preParsed[$sortingKey])) {
            $sorting = $this->parseSortingString($preParsed[$sortingKey]);
        }

        if (isset($preParsed[$fieldsKey])) {
            $fields = $this->parseFieldsString($preParsed[$fieldsKey]);
        }

        if (isset($preParsed[$limitKey])) {
            $limit = $this->parseLimitString($preParsed[$limitKey]);
        }

        return new DataQuery($queryString, $filters, $sorting, $fields, $limit);
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
     * @param $queryString
     * @return array
     */
    protected function parseFieldsString($queryString)
    {
        return explode(',', $queryString);
    }


    /**
     * @param $queryString
     * @return array
     */
    protected function parseLimitString($queryString)
    {
        $parts =  explode(',', $queryString);
        $limit = 0;
        $offset = 0;

        if (sizeof($parts) == 1) {
            $limit = intval($parts[0]);
        } else {
            $limit = intval($parts[0]);
            $offset = intval($parts[1]);
        }

        return new LimitRule($limit, $offset);
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
     * @param $fieldsKey
     * @return bool
     */
    protected function validateFieldsKey($fieldsKey)
    {
        if (!is_string($fieldsKey) || is_numeric($fieldsKey) || is_null($fieldsKey) || empty(($fieldsKey)) ) {
            throw new \InvalidArgumentException('The $fieldsKey parameter has to be of type string and must not be empty');
        }

        return true;
    }

    /**
     * @param $limitKey
     * @return bool
     */
    protected function validateLimitKey($limitKey)
    {
        if (!is_string($limitKey) || is_numeric($limitKey) || is_null($limitKey) || empty(($limitKey)) ) {
            throw new \InvalidArgumentException('The $limitKey parameter has to be of type string and must not be empty');
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