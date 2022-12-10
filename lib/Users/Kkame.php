<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;
use App\PlayerInfo;

class Kkame implements \App\UserInterface
{

    public function getName(): string
    {
        return 'Kkame';
    }

    public function getMessage(): string
    {
        $msg_list = [
            '올수있다 에카라쿠배!',
            '해외여행 얼마까지 알아보고 오셨어요?',
            '지원도 안할꺼면서 왜 맨날 찾아!',
            '방어 먹으러 가실래요?',
        ];
        shuffle($msg_list);
        return $msg_list[0];
    }

    public function action(PlayerInfo $playerInfo, array $tileInfoTable): ActionEnum
    {

        Game::BOOM_TURNS; // 해당 턴 수 마다 폭발이 발생 합니다.
        Game::mapColNum(); // 맵의 가로 개수
        Game::mapRowNum(); // 맵의 세로 개수

        $playerInfo->x; // 플레이어 가로 위치
        $playerInfo->y; // 플레이어 세로 위치
        $playerInfo->hp; // 플레이어 HP
        $playerInfo->shield; // 플레이어 보호막

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tileInfoTable as $y => $tile_info_rows) {
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