<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class Naledi implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '별';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '새해 복 많이 받으세요 🙇‍♀️',
            '해피 뉴 이어 🎉',
            '해피 홀리데이 🎅',
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
        $target_point = 0;
        $target_pos = [];

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부

                if (!$tile_info->exist_shield) {
                    continue;
                }

                $point = Game::mapColNum() * Game::mapRowNum();

                $abs_x = abs($player_info->x - $x);
                $abs_y = abs($player_info->y - $y);
                $distance_penalty = $abs_x + $abs_y;

                $point -= $distance_penalty;

                $player_penalty = (function () use (
                    $player_info,
                    $tile_info_table,
                    $x,
                    $y
                ) {
                    $penalty_high = 3;
                    $penalty_middle = 1;

                    $outline_data_list = [
                        [$penalty_high, $y, $x - 1],
                        [$penalty_high, $y, $x + 1],
                        [$penalty_high, $y - 1, $x],
                        [$penalty_high, $y + 1, $x],
                        [$penalty_middle, $y - 1, $x - 1],
                        [$penalty_middle, $y - 1, $x + 1],
                        [$penalty_middle, $y + 1, $x - 1],
                        [$penalty_middle, $y + 1, $x + 1],
                    ];

                    $penalty_sum = 0;
                    foreach ($outline_data_list as $outline_data) {
                        [$penalty, $out_y, $out_x] = $outline_data;

                        if (
                            $player_info->x == $out_x
                            && $player_info->y == $out_y
                        ) {
                            continue;
                        }

                        $tile_info = $tile_info_table[$out_y][$out_x] ?? null;
                        if (!$tile_info) {
                            continue;
                        }

                        if ($tile_info->exist_player) {
                            $penalty_sum += $penalty;
                        }
                    }

                    return $penalty_sum;
                })();

                $point -= $player_penalty;

                if ($point > $target_point) {
                    $target_point = $point;
                    $target_pos = [
                        'x' => $x,
                        'y' => $y,
                    ];
                }
            }
        }

        if (empty($target_pos)) {
            return ActionEnum::Hold;
        }

        if ($player_info->x < $target_pos['x']) {
            $action = ActionEnum::Right;
        } elseif ($player_info->x > $target_pos['x']) {
            $action = ActionEnum::Left;
        } elseif ($player_info->y < $target_pos['y']) {
            $action = ActionEnum::Down;
        } elseif ($player_info->y > $target_pos['y']) {
            $action = ActionEnum::Up;
        } else {
            $action = ActionEnum::Hold;
        }

        return $action;
    }
}
