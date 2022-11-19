<?php

declare(strict_types=1);

namespace App;

class Player
{
    public readonly string $id;

    public readonly string $name;

    private int $hp = 5;

    public function __construct(
        private readonly Map $map,
        private readonly UserInterface $user
    ) {
        $class_name = get_class($user);
        preg_match('/[^\\\.]+$/', $class_name, $matches);
        $this->id = $matches[0];

        $this->name = $this->user->getName();
    }

    private function getPos(): ?Pos
    {
        return $this->map->getPlayerPos($this);
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function damage(): void
    {
        if ($this->hp > 0) {
            --$this->hp;
        }

        if ($this->hp < 1) {
            $this->map->removePlayer($this);
        }
    }

    public function action(): void
    {
        if ($this->hp < 1) {
            return;
        }

        $pos = $this->getPos();
        $tile_info_table = $this->map->getTileInfoTable();
        $action_enum = $this->user->action($pos->x, $pos->y, $tile_info_table);
        $this->map->actionPlayer($this, $action_enum);
    }

    public function getInfo(): array
    {
        $pos = $this->getPos();
        if ($pos) {
            $x = $pos->x;
            $y = $pos->y;
        } else {
            $x = null;
            $y = null;
        }

        return [
            'type' => 'player',
            'id' => $this->id,
            'name' => $this->name,
            'hp' => $this->hp,
            'x' => $x,
            'y' => $y,
        ];
    }
}
