<?php

declare(strict_types=1);

namespace App;

class PlayerInfo
{
    /**
     * @param int $x 가로 위치
     * @param int $y 세로 위치
     * @param int $hp HP
     * @param int $shield 보호막
     */
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $hp,
        public readonly int $shield,
    ) {
    }
}
