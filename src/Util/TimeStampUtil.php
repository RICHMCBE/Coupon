<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Util;

use function count;
use function date;
use function explode;
use function mktime;
use const PHP_INT_MAX;

final class TimeStampUtil
{
    public static function convertToString(int $timeStamp): string
    {
        return date("Y-m-d-H-i-s", $timeStamp);
    }

    public static function convertToInt(array $elements): int
    {
        return mktime(
            (int) $elements[3],
            (int) $elements[4],
            (int) $elements[5],
            (int) $elements[1],
            (int) $elements[2],
            (int) $elements[0],
        );
    }
}