<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 로빈후드가 되어, 살아남을 클래스를 만들어 봐요
 * 참여기 : https://root-garage-60a.notion.site/4653de392ee5493d8787414257e3dded
 */
class Robin implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Robin';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '나의 활을 받아라',
            '가진것을 모두 내려놓으세요!',
            '나는 정의로운 도둑이!',
            '정의를 위해 일하고 있습니다',
        ];
        shuffle($msg_list);
        return $msg_list[0];
    }

    /**
     * 실드를 먼저 확보하고, 없으면 랜ㄴ덤하게
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

        // 에너지가 닳았으면 일단 한턴 정지

        // 가장 가까운 실드를 향하여 달려보자
        // 사용자의 패턴은 복불복으로 생각한다.
        // 1 실드 찾기
        // 2 가장 가까운 실드로 이동

        $player_info->x; // 플레이어 가로 위치
        $player_info->y; // 플레이어 세로 위치
        $player_info->hp; // 플레이어 HP
        $player_info->shield; // 플레이어 보호막
        
        // 2차원 배열 타일 정보 전체 확인
        $shields = []; // x*y의 배열
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
                if($tile_info->exist_shield) {
                    // 나와의 거리 계산
                    $distance = abs($x - $player_info->x) + abs($y - $player_info->y);
                    $shields[$distance] = [
                        'x' => $x,
                        'y' => $y,
                    ];
                }
            }
        }
        // 실드가 있으면 실드로
        if(count($shields) > 0) {
            $shield = reset($shields);
            $distanceX = $shield['x'] - $player_info->x;
            $distanceY = $shield['y'] - $player_info->y;
            // 먼쪽을 먼저가자
            if (abs($distanceX) >= abs($distanceY)) {
                // 좌우 먼저
                return $distanceX > 0 ? ActionEnum::Right : ActionEnum::Left;
            } else {
                // 위아래 먼저
                return $distanceY > 0 ? ActionEnum::Down : ActionEnum::Up;
            }
        } 

        // 없으면 랜덤
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
