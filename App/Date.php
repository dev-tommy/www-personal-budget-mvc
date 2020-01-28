<?php

namespace App;

class Date
{
    public static $months = array('styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień');

    public static function rawDate($convertedDate)
    {
        return strtotime($convertedDate);
    }

    public static function convertDate($rawDate)
    {
        return date("Y-m-d", $rawDate);
    }

    public static function getTodayDate()
    {
        return Date::convertDate(strtotime("today"));
    }

    public static function getCurrentMonthName()
    {
        return self::$months[date("m") - 1];
    }

    public static function getPreviousMonthName()
    {
        return self::$months[date("m", strtotime("-1 month"))-1];
    }

    public static function getCurrentYear()
    {
        return date("Y");
    }

    public static function getFirstDayOfCurrentMonth()
    {
        return Date::convertDate(strtotime(date("Y-m") . "-01"));
    }

    public static function getLastDayOfCurrentMonth()
    {
        return Date::convertDate(strtotime("+1 month, -1 day", strtotime(Date::getFirstDayOfCurrentMonth())));
    }

    public static function getFirstDayOfPreviousMonth()
    {
        return Date::convertDate(strtotime("-1 month", strtotime(Date::getFirstDayOfCurrentMonth())));
    }

    public static function getLastDayOfPreviousMonth()
    {
        return Date::convertDate(strtotime("+1 month, -1 day", strtotime(Date::getFirstDayOfPreviousMonth())));
    }

    public static function getFirstDayOfCurrentYear()
    {
        return Date::convertDate(strtotime(date("Y") . "-01-01"));
    }
}