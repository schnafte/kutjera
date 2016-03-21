<?php
namespace Schnafte\Kutjera;

/**
 * Class FilterRule
 * @package Kutjera
 */
class FilterRule
{
    const OP_EQ     = 'eq';
    const OP_NEQ    = 'neq';
    const OP_GT     = 'gt';
    const OP_GTE    = 'gte';
    const OP_LT     = 'lt';
    const OP_LTE    = 'lte';
    const OP_ANY    = 'any';
    const OP_CTS    = 'cts';
    const OP_LKE    = 'lke';


    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;


    /**
     * FilterRule constructor.
     * @param string $field
     * @param string $operator
     * @param string $value
     */
    public function __construct($field, $operator, $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }


    /*******************************************************************************************************************
     * PUBLIC
     ******************************************************************************************************************/


    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}