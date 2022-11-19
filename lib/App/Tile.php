<?php

declare(strict_types=1);

namespace App;

class Tile
{
    private ?Player $player = null;
    private bool $shield = false;

    public function __construct(
        public readonly Pos $pos
    ) {
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function hasPlayer(): bool
    {
        return !is_null($this->player);
    }

    public function setPlayer(?Player $player): void
    {
        if (!is_null($player) && !is_null($this->player)) {
            throw new \Exception('플레이어 충돌');
        }

        $this->player = $player;
    }

    public function setShield(bool $value): void
    {
        $this->shield = $value;
    }

    public function hasShield(): bool
    {
        return $this->shield;
    }

    public function getInfo(): TileInfo
    {
        $info = new TileInfo(
            exist_player: !is_null($this->player),
            exist_shield: $this->shield
        );

        return $info;
    }

    public function __toString(): string
    {
        return $this->player ? '[P]' : '[_]';
    }
}
