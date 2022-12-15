<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class IamBold implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '뭉지뭉지';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '나는빡빡이다',
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
        $i = 0;

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

        // 게임 조건
        // 쉴드가 0일 경우 HP가 0이면 게임 종료

        // 게임 진행
        // 1순위. 폭발을 피한다 -> 가능한지?
        // 2순위. 쉴드 생성 위치 정보를 얻는다 -> 쉴드가 있는 곳으로 이동한다

        // 1. 내 현재 위치 정보
        // 2. 쉴드 위치 정보
        // 3. 내 현재 위치 -> 쉴드 위치로 이동하는 로직 구현
        // 이걸 제가 해야된다구요??

        // 추가 고려사항. 플레이어가 이동하려는 타일에 다른 플레이어가 존재하면 이동이 불가하다

        /*
        * 특정 x,y 위치에 방어막이 있는지 확인하는 코드
        */


        // 정말 이렇게 밖에 코드를 작성하지 못해서 자괴감이 드네요..
        // 하지만 이게 제 실력인것을 받아들이고 풀리퀘스트 하겠습니다!!

        $tile_info1 = $tile_info_table[$player_info->y][$player_info->x];
        $tile_info2 = $tile_info_table[$player_info->y - 1][$player_info->x];
        $tile_info3 = $tile_info_table[$player_info->y + 1][$player_info->x];
        $tile_info4 = $tile_info_table[$player_info->y][$player_info->x - 1];
        $tile_info5 = $tile_info_table[$player_info->y][$player_info->x + 1];


        // 내 위치에 쉴드가 있을 경우 이동하지 않음
        if ($tile_info1->exist_shield) {
            $i = 0;
        } else if ($tile_info2->exist_shield) {
            $i = 1;
        } else if ($tile_info3->exist_shield) {
            $i = 2;
        } else if ($tile_info4->exist_shield) {
            $i = 3;
        } else if ($tile_info5->exist_shield) {
            $i = 4;
            // 내 위치와, 상하좌우에 쉴드가 존재하지 않을 경우
            // 운이 맡기며 이동!
        } else {
            $i = mt_rand(0, 4);
        }

        return match ($i) {
            0 => ActionEnum::Hold,
            1 => ActionEnum::Up,
            2 => ActionEnum::Down,
            3 => ActionEnum::Left,
            4 => ActionEnum::Right,
        };
    }
}
