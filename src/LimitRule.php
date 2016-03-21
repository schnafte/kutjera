<?php
namespace Kutjera;

/**
 * Class LimitRule
 * @package Kutjera
 */
class LimitRule
{
    /**
     * @var integer
     */
    private $count;

    /**
     * @var integer
     */
    private $offset;


    /**
     * @param int $count
     * @param int $offset
     */
    public function __construct($count = 0, $offset = 0)
    {
        $this->count = intval($count);
        $this->offset = intval($offset);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return bool
     */
    public function isDefined()
    {
        return $this->offset != 0 || $this->count != 0;
    }

}