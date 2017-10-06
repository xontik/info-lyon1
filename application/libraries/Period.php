<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 18/09/2017
 * Time: 10:31
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Period
{
    private $_beginDate;
    private $_endDate;

    public function __construct($beginDate, $endDate)
    {
        if (!$beginDate instanceof DateTime)
            throw new InvalidArgumentException('Begin date can only be a DateTime. Input was : ' . $date);
        $this->_beginDate = $beginDate;
        $this->setEndDate($endDate);
    }

    /**
     * Computes the number of days
     * @param null $date DateTime The date you want days from
     * @return int The number of days between the begin day and the date.
     */
    public function getDays($date = null) {
        if (is_null($date))
            $date = $this->_endDate;
        return $this->_beginDate->diff($date, true)->days;
    }

    /**
     * @param $date DateTime The date to be tested
     * @return bool Whether the date is in the period or not
     */
    public function hasDate($date) {
        return $this->_beginDate->diff($date)->invert === 0
            && $this->_endDate->diff($date)->invert === 1;
    }

    /**
     * @return DateTime The time at which begins the period
     */
    public function getBeginDate()
    {
        return $this->_beginDate;
    }

    /**
     * Change the time at which begins the period
     * @param $date DateTime The time at which begins the period
     * @throws TypeError
     */
    public function setBeginDate($date)
    {
        if (!$date instanceof DateTime)
            throw new InvalidArgumentException('Begin date can only be a DateTime. Input was : ' . $date);
        // If date after end date
        if ($date->diff($this->_endDate)->invert === 1)
            throw new DomainException('Begin date can\'t be after end date');
        $this->_beginDate = $date;
    }

    /**
     * @return DateTime The time at which ends the period
     */
    public function getEndDate()
    {
        return $this->_endDate;
    }

    /**
     * Change the time at which ends the period
     * @param $date DateTime The time at which ends the period
     * @throws TypeError
     */
    public function setEndDate($date)
    {
        if (!$date instanceof DateTime)
            throw new InvalidArgumentException('End date can only be a DateTime. Input was : ' . $date);
        // If date before begin date
        if ($this->_beginDate->diff($date)->invert === 1)
            throw new DomainException('End date can\'t be before begin date');
        $this->_endDate = $date;
    }

}