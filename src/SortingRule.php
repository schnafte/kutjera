<?php
namespace Kutjera;

/**
 * Class SortingRule
 * @package Kutjera
 */
class SortingRule
{
    const ORDER_ASC =   'ASC';
    const ORDER_DESC =  'DESC';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $order;


    /**
     * FilterRule constructor.
     * @param string $field
     * @param string $order
     */
    public function __construct($field, $order)
    {
        if (!in_array($order, [self::ORDER_ASC, self::ORDER_DESC])) {
            throw new \InvalidArgumentException('The $order parameter has to be one of the predefined enums: [\'ASC\',\'DESC\']');
        }

        $this->field = $field;
        $this->order = $order;
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
    public function getOrder()
    {
        return $this->order;
    }

}