<?php

namespace Systonia\Holidays;

/**
 * The public holidays of Germany by law, from 1995 on. Without a state only the nationwide ones count.
 * Holidays that only some municipalities have (Corpus Christi in parts of Saxony and Thuringia, Assumption
 * Day in Bavaria's Catholic municipalities) are not part of any state's list.
 */
final class Germany
{
    /** The first year the rules hold for: Repentance Day stopped being nationwide in 1995. */
    public const int FIRST_YEAR = 1995;

    /** @return list<Holiday> The holidays of $year, sorted by date. */
    public static function holidays(int $year, ?State $state = null): array
    {
        if ($year < self::FIRST_YEAR) {
            $first = self::FIRST_YEAR;
            throw new \InvalidArgumentException("Holidays before $first are not covered, not $year");
        }

        $easter = self::easter($year);
        $in = fn (State ...$states) => $state !== null && in_array($state, $states, true);

        $holidays = [
            self::on($year, 1, 1, 'Neujahr'),
            self::after($easter, -2, 'Karfreitag'),
            self::after($easter, 1, 'Ostermontag'),
            self::on($year, 5, 1, 'Tag der Arbeit'),
            self::after($easter, 39, 'Christi Himmelfahrt'),
            self::after($easter, 50, 'Pfingstmontag'),
            self::on($year, 10, 3, 'Tag der Deutschen Einheit'),
            self::on($year, 12, 25, '1. Weihnachtstag'),
            self::on($year, 12, 26, '2. Weihnachtstag'),
        ];

        if ($in(State::BadenWuerttemberg, State::Bavaria, State::SaxonyAnhalt)) {
            $holidays[] = self::on($year, 1, 6, 'Heilige Drei Könige');
        }
        $berlin = $in(State::Berlin) && $year >= 2019;
        $mecklenburg = $in(State::MecklenburgVorpommern) && $year >= 2023;
        if ($berlin || $mecklenburg) {
            $holidays[] = self::on($year, 3, 8, 'Internationaler Frauentag');
        }
        if ($in(State::Brandenburg)) {
            $holidays[] = self::after($easter, 0, 'Ostersonntag');
            $holidays[] = self::after($easter, 49, 'Pfingstsonntag');
        }
        // 75 and 80 years after the end of the war in Europe, once each
        if ($in(State::Berlin) && in_array($year, [2020, 2025], true)) {
            $holidays[] = self::on($year, 5, 8, 'Tag der Befreiung');
        }
        if ($in(
            State::BadenWuerttemberg,
            State::Bavaria,
            State::Hesse,
            State::NorthRhineWestphalia,
            State::RhinelandPalatinate,
            State::Saarland,
        )) {
            $holidays[] = self::after($easter, 60, 'Fronleichnam');
        }
        if ($in(State::Saarland)) {
            $holidays[] = self::on($year, 8, 15, 'Mariä Himmelfahrt');
        }
        if ($in(State::Thuringia) && $year >= 2019) {
            $holidays[] = self::on($year, 9, 20, 'Weltkindertag');
        }
        // Nationwide once for its 500th anniversary; four northern states added it in 2018
        $east = $in(
            State::Brandenburg,
            State::MecklenburgVorpommern,
            State::Saxony,
            State::SaxonyAnhalt,
            State::Thuringia,
        );
        $north = $in(State::Bremen, State::Hamburg, State::LowerSaxony, State::SchleswigHolstein);
        if ($year === 2017 || $east || ($north && $year >= 2018)) {
            $holidays[] = self::on($year, 10, 31, 'Reformationstag');
        }
        if ($in(
            State::BadenWuerttemberg,
            State::Bavaria,
            State::NorthRhineWestphalia,
            State::RhinelandPalatinate,
            State::Saarland,
        )) {
            $holidays[] = self::on($year, 11, 1, 'Allerheiligen');
        }
        if ($in(State::Saxony)) {
            // The Wednesday before 23 November
            $holidays[] = new Holiday(
                (new \DateTimeImmutable("$year-11-23"))->modify('last wednesday'),
                'Buß- und Bettag',
            );
        }

        usort($holidays, fn (Holiday $a, Holiday $b) => $a->date <=> $b->date);

        return $holidays;
    }

    /** The holiday on $date, or null for a working day (Sundays are not holidays by themselves). */
    public static function holiday(\DateTimeInterface $date, ?State $state = null): ?Holiday
    {
        $day = $date->format('Y-m-d');
        foreach (self::holidays((int) $date->format('Y'), $state) as $holiday) {
            if ($holiday->date->format('Y-m-d') === $day) {
                return $holiday;
            }
        }

        return null;
    }

    public static function isHoliday(\DateTimeInterface $date, ?State $state = null): bool
    {
        return self::holiday($date, $state) !== null;
    }

    /** Easter Sunday in the Gregorian calendar (Meeus), without ext-calendar. */
    public static function easter(int $year): \DateTimeImmutable
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return self::date($year, $month, $day);
    }

    private static function on(int $year, int $month, int $day, string $name): Holiday
    {
        return new Holiday(self::date($year, $month, $day), $name);
    }

    private static function after(\DateTimeImmutable $easter, int $days, string $name): Holiday
    {
        return new Holiday($easter->modify("$days days"), $name);
    }

    private static function date(int $year, int $month, int $day): \DateTimeImmutable
    {
        return new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));
    }
}
