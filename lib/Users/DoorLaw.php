<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class DoorLaw implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'DoorLaw';
    }

    /**
     * 사용자 액션
     *
     * @param int $player_x 플레이어 가로 위치
     * @param int $player_y 플레이어 세로 위치
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return \App\ActionEnum
     */
    public function action(int $player_x, int $player_y, array $tile_info_table): ActionEnum
    {
        // 해당 턴 수 마다 방어막 위치가 변경 됩니다.
        \App\Game::SHIELD_TURNS;

        // 해당 턴 수 마다 폭발이 발생 합니다.
        \App\Game::BOOM_TURNS;

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
            }
        }

        $i = mt_rand(0, 3);

        return match ($i) {
            0 => ActionEnum::Up,
            1 => ActionEnum::Down,
            2 => ActionEnum::Left,
            3 => ActionEnum::Right,
        };
    }
}
