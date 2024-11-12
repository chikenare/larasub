<?php

namespace Err0r\Larasub\Services;

use Err0r\Larasub\Enums\Period;

final class PeriodService
{
  public function getMinutes(int $value, Period $period): int
  {
    return match ($period) {
      Period::MINUTE => $value,
      Period::HOUR => $value * 60,
      Period::DAY => $value * 60 * 24,
      Period::WEEK => $value * 60 * 24 * 7,
      Period::MONTH => $value * 60 * 24 * 30,
      Period::YEAR => $value * 60 * 24 * 365,
    };
  }

  public function getDays(int $value, Period $period): int
  {
    return match ($period) {
      Period::MINUTE => $value / 60 / 24,
      Period::HOUR => $value / 24,
      Period::DAY => $value,
      Period::WEEK => $value * 7,
      Period::MONTH => $value * 30,
      Period::YEAR => $value * 365,
    };
  }

  public function getMonths(int $value, Period $period): int
  {
    return match ($period) {
      Period::MINUTE => $value / 60 / 24 / 30,
      Period::HOUR => $value / 24 / 30,
      Period::DAY => $value / 30,
      Period::WEEK => $value / 4,
      Period::MONTH => $value,
      Period::YEAR => $value * 12,
    };
  }
}
