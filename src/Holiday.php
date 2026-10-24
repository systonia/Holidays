<?php

namespace Systonia\Holidays;

/** A public holiday: its date and its official German name. */
final readonly class Holiday
{
    public function __construct(
        public \DateTimeImmutable $date,
        public string $name,
    ) {}
}
