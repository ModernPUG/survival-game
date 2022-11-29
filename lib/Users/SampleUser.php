<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class SampleUser implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'MPUG';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '끝까지 살아남자!',
            '아이쿠! 아파요~',
            '최선을 다해 피하는 중...',
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

        $i = mt_rand(0, 4);

        return match ($i) {
            0 => ActionEnum::Hold,
            1 => ActionEnum::Up,
            2 => ActionEnum::Down,
            3 => ActionEnum::Left,
            4 => ActionEnum::Right,
        };
    }
}
