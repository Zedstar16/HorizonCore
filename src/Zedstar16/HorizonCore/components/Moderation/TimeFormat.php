<?php


namespace Zedstar16\HorizonCore\components\Moderation;

use DateTime;
use TypeError;

class TimeFormat
{

    /** @var DateTime */
    private $date;

    public static function get(string $format = null)
    {
        if (is_numeric($format)) {
            if (intval($format) <= 0) {
                throw new ModerationException("0 and negative values are not allowed in time format.");
            }
            $dateTime = new DateTime();
            $dateTime->setTimestamp($dateTime->getTimestamp() + intval($format));
            return $dateTime;
        }
        $date = new DateTime();
        $second = 0;
        $minute = 0;
        $hour = 0;
        $day = 0;
        $week = 0;
        $month = 0;
        $year = 0;
        $decade = 0;
        $century = 0;
        $currentCharacters = "";
        $formatChars = str_split($format);
        for ($i = 0, $iMax = count($formatChars); $i < $iMax; $i++) {
            if (is_numeric($formatChars[$i])) {
                $currentCharacters .= $formatChars[$i];
                continue;
            }
            switch (strtolower($formatChars[$i])) {
                case "s":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter a valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    $second = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "m":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter a valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    if (isset($formatChars[$i + 1])) {
                        if (!is_numeric($formatChars[$i + 1])) {
                            switch (strtolower($formatChars[$i + 1])) {
                                case "o":
                                    if ((int)$currentCharacters <= 0) {
                                        throw new ModerationException("0 and negative values are not allowed in time format.");
                                    }
                                    $month = (int)$currentCharacters;
                                    $currentCharacters = "";
                                    break;
                                default:
                                    throw new ModerationException("Please enter a valid time format.");
                            }
                            $i += 1;
                            break;
                        }
                    }
                    $minute = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "h":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    $hour = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "d":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    if (isset($formatChars[$i + 1])) {
                        if (!is_numeric($formatChars[$i + 1])) {
                            switch (strtolower($formatChars[$i + 1])) {
                                case "c":
                                    if ((int)$currentCharacters <= 0) {
                                        throw new ModerationException("0 and negative values are not allowed in time format.");
                                    }
                                    if ($currentCharacters == "") {
                                        throw new ModerationException("Please enter an valid time format.");
                                    }
                                    $decade = (int)$currentCharacters;
                                    $currentCharacters = "";
                                    break;
                                default:
                                    throw new ModerationException("Please enter an valid time format.");
                            }
                            $i++;
                            break;
                        }
                    }
                    $day = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "w":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    $week = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "y":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    $year = (int)$currentCharacters;
                    $currentCharacters = "";
                    break;
                case "c":
                    if ($currentCharacters == "") {
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    if ((int)$currentCharacters <= 0) {
                        throw new ModerationException("0 and negative values are not allowed in time format.");
                    }
                    if (isset($formatChars[$i + 1])) {
                        if (!is_numeric($formatChars[$i + 1])) {
                            switch (strtolower($formatChars[$i + 1])) {
                                case "t":
                                    if ($currentCharacters == "") {
                                        throw new ModerationException("Please enter an valid time format.");
                                    }
                                    if ((int)$currentCharacters <= 0) {
                                        throw new ModerationException("0 and negative values are not allowed in time format.");
                                    }
                                    $century = (int)$currentCharacters;
                                    $currentCharacters = "";
                                    break;
                                default:
                                    throw new ModerationException("Please enter an valid time format.");
                            }
                            $i++;
                            break;
                        }
                        throw new ModerationException("Please enter an valid time format.");
                    }
                    break;
                default:
                    throw new ModerationException("Please enter an valid time format.");
            }
        }
        while ($second >= 60) {
            $minute++;
            $second -= 60;
        }
        while ($minute >= 60) {
            $hour++;
            $minute -= 60;
        }
        while ($hour >= 24) {
            $day++;
            $hour -= 24;
        }
        while ($week >= 1) {
            $day += 7;
            $week--;
        }
        while ($day >= 30) {
            $month++;
            $day -= 30;
        }
        while ($month >= 12) {
            $year++;
            $month -= 12;
        }
        while ($decade >= 1) {
            $year += 10;
            $decade--;
        }
        while ($century >= 1) {
            $year += 100;
            $century--;
        }
        $newSecond = (int)$date->format("s") + $second;
        $newMinute = (int)$date->format("i") + $minute;
        $newHour = (int)$date->format("H") + $hour;
        $newDay = (int)$date->format("d") + $day;
        $newMonth = (int)$date->format("m") + $month;
        $newYear = (int)$date->format("Y") + $year;
        $newDate = new DateTime();
        $newDate = $newDate->setDate($newYear, $newMonth, $newDay);
        $newDate = $newDate->setTime($newHour, $newMinute, $newSecond);
        return $newDate;
    }

    public static function getCurrentDate()
    {
        return date("r", time());
        // return date("D jS F Y", time());
    }

    public static function timeToExpiry($time): string
    {
        if (is_string($time)) {
            $timestamp = strtotime($time) - time();
        } elseif ($time instanceof DateTime) {
            $timestamp = $time->getTimestamp() - time();
        } elseif (is_int($time)) {
            $timestamp = $time - time();
        } else {
            throw new TypeError("Expected String/DateTime/Int, got " . gettype($time));
        }
        $c = new DateTime('@0');
        $then = new DateTime("@$timestamp");
        $time = explode(":", $c->diff($then)->format('%a:%h:%i'));
        return "§b$time[0] §3days §b$time[1] §3hrs §b$time[1] §3mins";
    }

    public static function expirationTimerToString(DateTime $from, DateTime $to): string
    {
        $string = "";
        $second = (int)$from->format("s") - (int)$to->format("s");
        $minute = (int)$from->format("i") - (int)$to->format("i");
        $hour = (int)$from->format("H") - (int)$to->format("H");
        $day = (int)$from->format("d") - (int)$to->format("d");
        $month = (int)$from->format("n") - (int)$to->format("n");
        $year = (int)$from->format("Y") - (int)$to->format("Y");
        if ($second <= -1) {
            $second = 60 + $second;
            $minute--;
        }
        if ($minute <= -1) {
            $minute = 60 + $minute;
            $hour--;
        }
        if ($hour <= -1) {
            $hour = 24 + $hour;
            $day--;
        }
        if ($day <= -1) {
            $day = 30 + $day;
            $month--;
        }
        if ($month <= -1) {
            $month = 12 + $month;
            $year--;
        }
        $string .= $year >= 1 ? strval($year) . " " . "Y " : "";
        $string .= $month >= 1 ? strval($month) . " " . "M " : "";
        $string .= $day >= 1 ? strval($day) . " " . "D " : "";
        $string .= $hour >= 1 ? strval($hour) . " " . "H " : "";
        $string .= $minute >= 1 ? strval($minute) . " " . "m " : "";
        $string = substr($string, 0, strlen($string) - 1);
        return $string;
    }
}