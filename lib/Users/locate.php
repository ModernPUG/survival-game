<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 참가자 17
 * 특출난건 없습니다. 그냥 참가자 입니다.
 */
class locate implements \App\UserInterface
{
    /** 캐릭터 이름
     */
    public function getName(): string
    {
        return '참가자17';
    }

    // 멘트
    public function getMessage(): string
    {
        $message = [
            '지더라도 열심히!',
            '시간은 많이썼는데',
            '기발하지도 않고',
            '평범 그자체!',
        ];
        shuffle($message);

        return $message[0];
    }

    /** 절대값 구하기.
     *
     */
    public function absoluteValue($value)
    {
        if ($value < 0) {
            $value = $value * -1;
        }

        return $value;
    }

    /* * 사용자 액션
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

        $shieldArray = []; //  방어막 좌표 리스트
        $shieldDistanceArray = []; // 방어막 거리 배열
        $shieldIndex = 0;

        $playerArray = []; //  방어막 좌표 리스트
        $playerDistanceArray = []; // 방어막 거리 배열
        $playerIndex = 0;

        foreach ($tile_info_table as $y => $tile_info_rows) {
            /**
             * @var \App\TileInfo $tile_info
             * */
            foreach ($tile_info_rows as $x => $tile_info) {
                 // 가로 위치
                 // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
                $distanceX = $this->absoluteValue($x - $player_info->x); // 방어막까지의 가로 거리
                $distanceY = $this->absoluteValue($y - $player_info->y); // 방어막까지의 세로 거리
                $distance = $distanceX + $distanceY;

                // 방어막 Array
                if (1 == $tile_info->exist_shield) {
                    // 가장 가까운 거리의 방어막을 찾기 위해 방어막의 거리 배열 생성
                    $shieldDistanceArray[$shieldIndex] = $distance;
                    $shieldArray[$shieldIndex] = [
                        'x' => $x,
                        'y' => $y,
                        'distance' => $distance,
                    ];
                    ++$shieldIndex;
                }

                /* 플레이어 Array
                 * 가장 가까운 것은 플레이어 자신임으로 타 플레이어는 거리가 0이상 차이 나야함
                 */
                if (1 == $tile_info->exist_player && $distance > 0) {
                    // 가장 가까운 거리의 방어막을 찾기 위해 방어막의 거리 배열 생성
                    $playerDistanceArray[$playerIndex] = $distance;

                    $playerArray[$playerIndex] = [
                        'x' => $x,
                        'y' => $y,
                        'distance' => $distance,
                    ];
                    ++$playerIndex;
                }
            }
        }

        // 방어막 거리 배얼을 거리순으로 정렬
        asort($shieldDistanceArray);
        asort($playerDistanceArray);

        // 가장 가까운 거리의 key를 가져옴
        $sheildNearestKey = array_key_first($shieldDistanceArray);
        $playerNearestKey = array_key_first($playerDistanceArray);

        // 가장 가까운 거리의 플레이어 좌표
        $playerX = $playerArray[$playerNearestKey]['x'];
        $playerY = $playerArray[$playerNearestKey]['y'];

        // 가장 가까운 거리의 플레이어 까지의 거리
        $playerDistansX = $playerX - $player_info->x;
        $playerDistansY = $playerY - $player_info->y;
        $playerDistance = $this->absoluteValue($playerDistansX) + $this->absoluteValue($playerDistansY);

        // 가장 가까운 거리의 방어막 좌표
        $shieldX = $shieldArray[$sheildNearestKey]['x'];
        $shieldY = $shieldArray[$sheildNearestKey]['y'];

        // 가장 가까운 거리의 방어막 까지의 거리
        $shieldDistansX = $shieldX - $player_info->x;
        $shieldDistansY = $shieldY - $player_info->y;

        // 단순이동 
        //
        // X축 이동
        if (0 == $shieldDistansX) {
            // Y축 이동
            if ($shieldDistansY > 0) {
                $i = 2;
            } else {
                $i = 1;
            }
        } elseif ($shieldDistansX < 0) {
            $i = 3;
        } elseif ($shieldDistansX > 0) {
            $i = 4;
        } else {
            $i = rand(0, 4);
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
