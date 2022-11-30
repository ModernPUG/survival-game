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

    public function __toString()
    {
        return "{$this->x}-{$this->y}";
    }
}
