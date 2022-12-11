<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 요즘 한달에 160km 이상 달리고 있는 러너로서 해피러너 캐릭터를 만들었습니다.
 * 내년 3월 첫 마라톤 풀코스 도전에서 무사히 완주할 수 있길 기원합니다.
 * (그러나 이미지는 아무 상관없음)
 */
class HappyRunner implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '해피러너';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '달립니다',
            '비가 와도 눈이 와도 달립니다',
            '너무 아프면 안 달립니다',
            '최선을 다해 달립니다',
            '달리기 위해 달립니다',
            '휴식도 훈련입니다',
            '러너스 하이 상태 돌입!'
        ];
        shuffle($msg_list);
        return $msg_list[0];
    }

    /**
     * 사용자 액션
     *
     * @param \App\PlayerInfo $player_info 플레이어 정보
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return \App\ActionEnum
     */
    public function action(\App\PlayerInfo $player_info, array $tile_info_table): ActionEnum
    {
        Game::BOOM_TURNS; // 해당 턴 수 마다 폭발이 발생 합니다.
        Game::mapColNum(); // 맵의 가로 개수
        Game::mapRowNum(); // 맵의 세로 개수

        $player_info->x; // 플레이어 가로 위치
        $player_info->y; // 플레이어 세로 위치
        $player_info->hp; // 플레이어 HP
        $player_info->shield; // 플레이어 보호막
        $shield_tile_list = array();

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부

                // 실드 있고 플레이어 없는 타일 리스트 수집
                if ($tile_info->exist_shield && !$tile_info->exist_player) {
                    $distance_to_this_shield = $this->calculateDistanceFromMe($player_info->x, $player_info->y, $x, $y);
                    $shield_tile_list[] = [
                        'distance' => $distance_to_this_shield,
                        'tile' => [
                            'x' => $x,
                            'y' => $y
                        ],
                    ];
                }
            }
        }

        return $this->moveToShield($shield_tile_list, $player_info, $tile_info_table);
    }

    /**
     * @param int $player_x
     * @param int $player_y
     * @param int $tile_x
     * @param int $tile_y
     * @return int
     */
    private function calculateDistanceFromMe(int $player_x, int $player_y, int $tile_x, int $tile_y): int
    {
        $distance_x = abs($player_x - $tile_x);
        $distance_y = abs($player_y - $tile_y);
        return $distance_x + $distance_y;
    }

    /**
     * @param array $shield_tile_list
     * @param \App\PlayerInfo $player_info
     * @param array $tile_info_table
     * @return ActionEnum
     */
    private function moveToShield(array $shield_tile_list, \App\PlayerInfo $player_info, array $tile_info_table): ActionEnum
    {
        // 실드가 있는 타일 정보를 거리가 짧은 순으로 정렬
        $distances = array();
        foreach ($shield_tile_list as $key => $row) {
            $distances[$key] = $row['distance'];
        }
        array_multisort($distances, SORT_ASC, $shield_tile_list);

        if (empty($shield_tile_list) || $shield_tile_list[0]['distance'] > 8){
            $direction_x = $player_info->x - (int)(Game::mapRowNum() / 2);
            $direction_y = $player_info->y - (int)(Game::mapColNum() / 2);
        } else {
            $direction_x = $player_info->x - $shield_tile_list[0]['tile']['x'];
            $direction_y = $player_info->y - $shield_tile_list[0]['tile']['y'];
        }

        if ($direction_x == 0 && $direction_y == 0) {
            return ActionEnum::Hold;
        }
        if ($direction_x < 0 && $direction_y == 0) {
            return ActionEnum::Right;
        }
        if ($direction_x > 0 && $direction_y == 0) {
            return ActionEnum::Left;
        }
        if ($direction_x == 0 && $direction_y < 0) {
            return ActionEnum::Down;
        }
        if ($direction_x == 0 && $direction_y > 0) {
            return ActionEnum::Up;
        }
        $i = mt_rand(0, 1);
        if ($direction_x < 0 && $direction_y < 0) {
            if ($i == 0 && $tile_info_table[$player_info->y + 1][$player_info->x]->exist_player){
                $i = 1;
            }
            if ($i == 1 && $tile_info_table[$player_info->y][$player_info->x + 1]->exist_player){
                $i = 0;
            }
            return match ($i) {
                0 => ActionEnum::Down,
                1 => ActionEnum::Right,
            };
        }
        if ($direction_x > 0 && $direction_y < 0) {
            if ($i == 0 && $tile_info_table[$player_info->y + 1][$player_info->x]->exist_player){
                $i = 1;
            }
            if ($i == 1 && $tile_info_table[$player_info->y][$player_info->x - 1]->exist_player){
                $i = 0;
            }
            return match ($i) {
                0 => ActionEnum::Down,
                1 => ActionEnum::Left,
            };
        }
        if ($direction_x < 0 && $direction_y > 0) {
            if ($i == 0 && $tile_info_table[$player_info->y - 1][$player_info->x]->exist_player){
                $i = 1;
            }
            if ($i == 1 && $tile_info_table[$player_info->y][$player_info->x + 1]->exist_player){
                $i = 0;
            }
            return match ($i) {
                0 => ActionEnum::Up,
                1 => ActionEnum::Right,
            };
        }
        if ($direction_x > 0 && $direction_y > 0) {
            if ($i == 0 && $tile_info_table[$player_info->y - 1][$player_info->x]->exist_player){
                $i = 1;
            }
            if ($i == 1 && $tile_info_table[$player_info->y][$player_info->x - 1]->exist_player){
                $i = 0;
            }
            return match ($i) {
                0 => ActionEnum::Up,
                1 => ActionEnum::Left,
            };
        }
    }
}
