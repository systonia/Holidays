# Holidays

The public holidays of Germany by law, nationwide and for each of the 16 states, from 1995 on. Plain PHP, no
dependencies.

```bash
composer require systonia/holidays
```

```php
use Systonia\Holidays\{Germany, State};

foreach (Germany::holidays(2026, State::Bavaria) as $holiday) {
    echo $holiday->date->format('d.m.Y'), ' ', $holiday->name, "\n";   // 01.01.2026 Neujahr, 06.01.2026 Heilige Drei Könige, ...
}

Germany::isHoliday(new DateTimeImmutable('2026-10-31'), State::Hamburg);   // true
Germany::holiday(new DateTimeImmutable('2026-12-25'))?->name;              // "1. Weihnachtstag"
Germany::easter(2026);                                                     // 2026-04-05
```

- `holidays(int $year, ?State $state = null)`: the holidays of a year, sorted by date. Without a state only the
  nine nationwide ones.
- `holiday(DateTimeInterface $date, ?State $state = null)`: the `Holiday` on that date, or `null`. The time of day
  does not matter.
- `isHoliday(DateTimeInterface $date, ?State $state = null)`: whether it is one.
- `easter(int $year)`: Easter Sunday, computed without `ext-calendar`.

A `Holiday` has `date` (a `DateTimeImmutable` at midnight in PHP's default time zone) and `name`, the official
German name. `State` uses the official codes: `State::from('NW')`.

## What counts

- Holidays that changed are covered by year: Reformation Day nationwide in 2017 and in Bremen, Hamburg, Lower Saxony
  and Schleswig-Holstein since 2018, International Women's Day in Berlin since 2019 and in Mecklenburg-Vorpommern
  since 2023, World Children's Day in Thuringia since 2019, and Berlin's Day of Liberation in 2020 and 2025.
- Holidays that only some municipalities have are left out: Corpus Christi in parts of Saxony and Thuringia, Assumption
  Day in the Catholic municipalities of Bavaria, and Augsburg's Peace Festival.
- Easter Sunday and Whit Sunday count only where the law names them (Brandenburg); elsewhere they are Sundays.
- Years before 1995 throw `InvalidArgumentException`: Repentance Day was nationwide until 1994.

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan
```

## License

MIT or Apache-2.0, at your option.
