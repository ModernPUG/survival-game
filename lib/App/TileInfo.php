<?php

declare(strict_types=1);

namespace App;

class TileInfo
{
    public function __construct(
        public readonly bool $exist_player,
        public readonly bool $exist_shield
    ) {
    }
}
