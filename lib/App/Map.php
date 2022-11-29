<?php

declare(strict_types=1);

namespace App;

class Map
{
    /** @var Tile[][] */
    private array $tile_table = [];

    /** @var \SplObjectStorage<Tile> */
    private \SplObjectStorage $tile_list;

    /** @var array<string,Tile> */
    private array $tile_by_player = [];

    private int $shield_count = 0;

    public function __construct(
        private int $col_num,
        private int $row_num,
    ) {
        $this->tile_list = new \SplObjectStorage();

        for ($y = 0; $y < $this->row_num; $y++) {
            for ($x = 0; $x < $this->col_num; $x++) {
                $pos = new Pos($x, $y);
                $tile = new Tile($pos);
                $this->tile_table[$y][$x] = $tile;
                $this->tile_list->attach($tile);
            }
        }
    }

    public function getPlayerCount(): int
    {
        return count($this->tile_by_player);
    }

    public function getShieldCount(): int
    {
        return $this->shield_count;
    }

    /**
     * @return Tile[]
     */
    private function noplayerTileList(): array
    {
        /** @var Tile[] $noplayer_tile_list */
        $noplayer_tile_list = [];
        foreach ($this->tile_list as $tile) {
            if (!$tile->getPlayer()) {
                $noplayer_tile_list[] = $tile;
            }
        }

        return $noplayer_tile_list;
    }

    /**
     * @return Pos[]
     */
    public function noplayerPosList(): array
    {
        $noplayer_tile_list = $this->noplayerTileList();

        $pos_list = [];
        foreach ($noplayer_tile_list as $tile) {
            $pos_list[] = $tile->pos;
        }

        return $pos_list;
    }

    /**
     * @return Pos[]
     */
    public function shieldPosList(): array
    {
        $pos_list = [];
        foreach ($this->tile_list as $tile) {
            if ($tile->hasShield()) {
                $pos_list[] = $tile->pos;
            }
        }

        return $pos_list;
    }

    /**
     * @return Pos[]
     */
    public function noshieldPosList(): array
    {
        $pos_list = [];
        foreach ($this->tile_list as $tile) {
            if (!$tile->hasShield()) {
                $pos_list[] = $tile->pos;
            }
        }

        return $pos_list;
    }

    private function randNoplayerTile(): ?Tile
    {
        $noplayer_tile_list = $this->noplayerTileList();
        if (!$noplayer_tile_list) {
            return null;
        }

        $rand_key = array_rand($noplayer_tile_list, 1);
        $rand_tile = $noplayer_tile_list[$rand_key];

        return $rand_tile;
    }

    private function moveToPlayer(Player $player, int $x, int $y): void
    {
        $current_tile = $this->tile_by_player[$player->id] ?? null;
        if ($current_tile) {
            $current_tile->setPlayer(null);
        }

        $target_tile = $this->tile_table[$y][$x];
        $target_tile->setPlayer($player);
        $this->tile_by_player[$player->id] = $target_tile;
    }

    public function addPlayer(Player $player): void
    {
        $result = isset($this->tile_by_player[$player->id]);
        if ($result) {
            throw new \Exception('플레이어 중복');
        }

        $rand_tile = $this->randNoplayerTile();
        if (!$rand_tile) {
            throw new \Exception('빈 자리 없음');
        }

        $this->moveToPlayer(
            $player,
            $rand_tile->pos->x,
            $rand_tile->pos->y
        );
    }

    public function removePlayer(Player $player): void
    {
        $tile = $this->tile_by_player[$player->id];
        $tile->setPlayer(null);
        unset($this->tile_by_player[$player->id]);
    }

    public function getPlayerPos(Player $player): ?Pos
    {
        $tile = $this->tile_by_player[$player->id] ?? null;
        return $tile ? $tile->pos : null;
    }

    public function getPlayerByPos(Pos $pos): ?Player
    {
        $tile = $this->tile_table[$pos->y][$pos->x] ?? null;
        return $tile ? $tile->getPlayer() : null;
    }

    public function actionPlayer(Player $player, ActionEnum $action_enum): void
    {
        $pos = $this->getPlayerPos($player);
        $pos_x = $pos->x;
        $pos_y = $pos->y;

        switch ($action_enum) {
            case ActionEnum::Hold:
                return;

            case ActionEnum::Up:
                $pos_y -= 1;
                break;

            case ActionEnum::Down:
                $pos_y += 1;
                break;

            case ActionEnum::Left:
                $pos_x -= 1;
                break;

            case ActionEnum::Right:
                $pos_x += 1;
                break;
        }

        if (
            $pos_x < 0 || $pos_x > $this->col_num - 1
            || $pos_y < 0 || $pos_y > $this->row_num - 1
        ) {
            return;
        }

        $tile = $this->tile_table[$pos_y][$pos_x];

        if ($tile->hasPlayer()) {
            return;
        }

        $this->moveToPlayer($player, $pos_x, $pos_y);
    }

    public function resetAllShield(): void
    {
        foreach ($this->tile_list as $tile) {
            $tile->setShield(false);
        }
    }

    public function addShield(Pos $pos): void
    {
        $tile = $this->tile_table[$pos->y][$pos->x];
        $tile->setShield(true);
        ++$this->shield_count;
    }

    public function removeShield(Pos $pos): void
    {
        $tile = $this->tile_table[$pos->y][$pos->x];
        $tile->setShield(false);
        --$this->shield_count;
    }

    public function hasShieldByPos(Pos $pos): bool
    {
        $tile = $this->tile_table[$pos->y][$pos->x];
        return $tile->hasShield();
    }

    /**
     * @return TileInfo[][]
     */
    public function getTileInfoTable(): array
    {
        $info_table = [];

        foreach ($this->tile_table as $y => $row_tiles) {
            foreach ($row_tiles as $x => $tile) {
                $info_table[$y][$x] = $tile->getInfo();
            }
        }

        return $info_table;
    }

    public function getTileDataList(): array
    {
        $info_list = [];

        foreach ($this->tile_table as $y => $row_tiles) {
            foreach ($row_tiles as $x => $tile) {
                $player = $tile->getPlayer();
                $player_info = $player ? $player->getInfo() : null;

                $info_list[] = [
                    'x' => $x,
                    'y' => $y,
                    'player' => $player_info,
                ];
            }
        }

        return $info_list;
    }
}
