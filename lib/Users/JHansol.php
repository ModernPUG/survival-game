<?php

namespace Users;

use App\ActionEnum;
use App\TileInfo;
use App\UserInterface;

class JHansol implements UserInterface {
    private const UP        = 0;
    private const DOWN      = 1;
    private const LEFT      = 2;
    private const RIGHT     = 3;
    private const NONE      = -1;

    public function getName(): string {
        return 'j-Hansol';
    }

    public function action(int $player_x, int $player_y, array $tile_info_table): ActionEnum {
        $t = [
            self::UP => $player_y == 0 ? null : $tile_info_table[$player_y - 1][$player_x],
            self::DOWN => count($tile_info_table) - 1 == $player_y ? null : $tile_info_table[$player_y + 1][$player_x],
            self::LEFT => $player_x == 0 ? null : $tile_info_table[$player_x - 1][$player_y],
            self::RIGHT => count($tile_info_table[$player_y]) - 1 == $player_x ? null : $tile_info_table[$player_x - 1][$player_y]
        ];

        $shields = [];
        $blanks = [];
        $direction = self::NONE;

        foreach($t as $idx => $val) {
            if($val) {
                if($val->exist_shield && !$val->exist_player) $shields[] = $idx;
                else if(!$val->exist_player) $blanks[] = $idx;
            }
        }

        if(count($shields) > 0) $direction = $shields[mt_rand(0, count($shields) - 1)];
        else if(count($blanks) > 0) $direction = $blanks[mt_rand(0, count($blanks) - 1)];
        else $direction = mt_rand(0, 3);

        return match ($direction) {
            0 => ActionEnum::Up,
            1 => ActionEnum::Down,
            2 => ActionEnum::Left,
            3 => ActionEnum::Right,
        };
    }
}