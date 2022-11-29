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
        // 이동 가능 타일 점검
        // 총 4개 중 타일의 가장자리에서 이동을 체크한 후 이동 가능하면 타일 정보를 그렇지 않으면 Null 지정
        $t = [
            self::UP => $player_y == 0 ? null : $tile_info_table[$player_y - 1][$player_x],
            self::DOWN => count($tile_info_table) - 1 == $player_y ? null : $tile_info_table[$player_y + 1][$player_x],
            self::LEFT => $player_x == 0 ? null : $tile_info_table[$player_x - 1][$player_y],
            self::RIGHT => count($tile_info_table[$player_y]) - 1 == $player_x ? null : $tile_info_table[$player_x - 1][$player_y]
        ];

        // 이동 가능한 방어막 타일
        $shields = [];
        // 이동 가능한 빈 공간 타일
        $blanks = [];
        // 이동 방향
        $direction = self::NONE;

        // 4개의 타일중 이동 가능한 타일정보 추출
        foreach($t as $idx => $val) {
            if($val) {
                if($val->exist_shield && !$val->exist_player) $shields[] = $idx;
                else if(!$val->exist_player) $blanks[] = $idx;
            }
        }

        // 이동 가능한 바어막 타일이 있는 경우 바어막 중 임의의 하나 선택 방어막이 없고 빈공간이 있는 경우 역시 빈 공간 중 하나를 선택,
        // 그렇지 않으면 4방향 중 임의의 방향 선택
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