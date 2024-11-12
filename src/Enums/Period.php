<?php

namespace Err0r\Larasub\Enums;

enum Period: string
{
    case MINUTE = 'minute';

    case HOUR = 'hour';

    case YEAR = 'year';

    case MONTH = 'month';

    case WEEK = 'week';

    case DAY = 'day';
}
