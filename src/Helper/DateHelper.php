<?php

namespace App\Helper;

use DateTime;

class DateHelper
{
    /**
     * YYYY-MM-DD
     *
     * @param DateTime $dateTime
     * @return string
     */
    public static function formatDate(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d');
    }

    /**
     * HH:MM:SS
     *
     * @param DateTime $dateTime
     * @return string
     */
    public static function formatTime(DateTime $dateTime): string
    {
        return $dateTime->format('H:i:s');
    }

    /**
     * Compares two dates and returns the difference in hours.
     *
     * @param DateTime $startTime
     * @param DateTime $endTime
     * @return int
     */
    public static function diffInHours(DateTime $startTime, DateTime $endTime): int
    {
        $diff = $startTime->diff($endTime);
        $hours = $diff->h + ($diff->days * 24);
        return $hours;
    }

    /**
     * Compares two dates and returns the difference in days.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return int
     */
    public static function diffInDays(DateTime $startDate, DateTime $endDate): int
    {
        $diff = $startDate->diff($endDate);
        $days = $diff->days;
        return $days;
    }
}
