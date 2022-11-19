<?php

declare(strict_types=1);

namespace App;

class Pos
{
    public function __construct(
        public readonly int $x,
        public readonly int $y
    ) {
    }
}
