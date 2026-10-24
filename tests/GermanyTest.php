<?php

namespace Systonia\Holidays\Tests;

use PHPUnit\Framework\TestCase;
use Systonia\Holidays\{Germany, Holiday, State};

final class GermanyTest extends TestCase
{
    /** @return array<string, string> date => name */
    private function list(int $year, ?State $state = null): array
    {
        $list = [];
        foreach (Germany::holidays($year, $state) as $holiday) {
            $list[$holiday->date->format('Y-m-d')] = $holiday->name;
        }

        return $list;
    }

    public function testEasterFallsOnKnownSundays(): void
    {
        $known = [
            1995 => '1995-04-16',
            2008 => '2008-03-23',
            2019 => '2019-04-21',
            2024 => '2024-03-31',
            2025 => '2025-04-20',
            2026 => '2026-04-05',
            2038 => '2038-04-25',
            2285 => '2285-03-22',
        ];
        foreach ($known as $year => $date) {
            $this->assertSame($date, Germany::easter($year)->format('Y-m-d'), (string) $year);
        }
    }

    public function testNationwideHolidaysOf2026(): void
    {
        $this->assertSame([
            '2026-01-01' => 'Neujahr',
            '2026-04-03' => 'Karfreitag',
            '2026-04-06' => 'Ostermontag',
            '2026-05-01' => 'Tag der Arbeit',
            '2026-05-14' => 'Christi Himmelfahrt',
            '2026-05-25' => 'Pfingstmontag',
            '2026-10-03' => 'Tag der Deutschen Einheit',
            '2026-12-25' => '1. Weihnachtstag',
            '2026-12-26' => '2. Weihnachtstag',
        ], $this->list(2026));
    }

    public function testBavariaAddsItsCatholicHolidays(): void
    {
        $bavaria = $this->list(2026, State::Bavaria);

        $this->assertCount(12, $bavaria);
        $this->assertSame('Heilige Drei Könige', $bavaria['2026-01-06']);
        $this->assertSame('Fronleichnam', $bavaria['2026-06-04']);
        $this->assertSame('Allerheiligen', $bavaria['2026-11-01']);
        $this->assertArrayNotHasKey('2026-08-15', $bavaria);
    }

    public function testEveryStateHasItsNumberOfHolidaysIn2026(): void
    {
        $counts = [
            'BW' => 12, 'BY' => 12, 'BE' => 10, 'BB' => 12, 'HB' => 10, 'HH' => 10, 'HE' => 10, 'MV' => 11,
            'NI' => 10, 'NW' => 11, 'RP' => 11, 'SL' => 12, 'SN' => 11, 'ST' => 11, 'SH' => 10, 'TH' => 11,
        ];
        foreach (State::cases() as $state) {
            $this->assertCount($counts[$state->value], Germany::holidays(2026, $state), $state->name);
        }
    }

    public function testRepentanceDayIsTheWednesdayBeforeTheTwentyThird(): void
    {
        $this->assertSame('Buß- und Bettag', $this->list(2026, State::Saxony)['2026-11-18']);
        // 23 November 2022 was a Wednesday itself: the day is a week earlier
        $this->assertSame('Buß- und Bettag', $this->list(2022, State::Saxony)['2022-11-16']);
    }

    public function testHolidaysThatStartedOrHappenedOnce(): void
    {
        $this->assertArrayHasKey('2017-10-31', $this->list(2017));
        $this->assertArrayNotHasKey('2018-10-31', $this->list(2018));
        $this->assertArrayHasKey('2018-10-31', $this->list(2018, State::Hamburg));
        $this->assertArrayNotHasKey('2016-10-31', $this->list(2016, State::Hamburg));

        $this->assertArrayNotHasKey('2018-03-08', $this->list(2018, State::Berlin));
        $this->assertArrayHasKey('2019-03-08', $this->list(2019, State::Berlin));
        $this->assertArrayNotHasKey('2022-03-08', $this->list(2022, State::MecklenburgVorpommern));
        $this->assertArrayHasKey('2023-03-08', $this->list(2023, State::MecklenburgVorpommern));

        $this->assertArrayHasKey('2025-05-08', $this->list(2025, State::Berlin));
        $this->assertArrayNotHasKey('2026-05-08', $this->list(2026, State::Berlin));
    }

    public function testADateIsAHolidayOrNot(): void
    {
        $holiday = Germany::holiday(new \DateTimeImmutable('2026-12-25 18:30'));
        $this->assertInstanceOf(Holiday::class, $holiday);
        $this->assertSame('1. Weihnachtstag', $holiday->name);

        $this->assertTrue(Germany::isHoliday(new \DateTime('2026-11-01'), State::NorthRhineWestphalia));
        $this->assertFalse(Germany::isHoliday(new \DateTime('2026-11-01'), State::Berlin));
        $this->assertFalse(Germany::isHoliday(new \DateTime('2026-11-02')));
    }

    public function testYearsBeforeTheRulesAreRefused(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Germany::holidays(1994);
    }
}
