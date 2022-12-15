<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;
use App\PlayerInfo;
use App\TileInfo;

class Woongbin implements \App\UserInterface
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const UP = 'up';
    public const DOWN = 'down';

    protected $moveData = [
        self::LEFT => ActionEnum::Left,
        self::RIGHT => ActionEnum::Right,
        self::UP => ActionEnum::Up,
        self::DOWN => ActionEnum::Down
    ];

    protected $maxX;
    protected $maxY;

    public function __construct()
    {
        $this->maxX = Game::mapColNum();
        $this->maxY = Game::mapRowNum();
    }

    public function getName(): string
    {
        return 'Woongbin';
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
        $crossPoints[self::LEFT] = $this->getLeftTilePoint($player_info);
        $crossPoints[self::RIGHT] = $this->getRightTilePoint($player_info);
        $crossPoints[self::UP] = $this->getUpTilePoint($player_info);
        $crossPoints[self::DOWN] = $this->getDownTilePoint($player_info);

        try {
            foreach ($crossPoints as $type => $point) {
                if ($point === null) {
                    continue;
                }

                $targetTile = $tile_info_table[$point[0]][$point[1]];
                if ($targetTile->exist_player === true) {
                    continue;
                }

                if ($targetTile->exist_shield === true) {
                    return $this->moveData[$type];
                }
            }

            shuffle($crossPoints);
            foreach ($crossPoints as $type => $point) {
                if ($point === null) {
                    continue;
                }

                $targetTile = $tile_info_table[$point[0]][$point[1]];
                if ($targetTile->exist_player === true) {
                    continue;
                }

                return $this->moveData[$type];
            }
        } catch (\Throwable $e) {
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

    protected function getLeftTilePoint(PlayerInfo $playerInfo)
    {
        if ($playerInfo->x === 0) {
            return null;
        }

        return [$playerInfo->x - 1, $playerInfo->y];
    }

    protected function getRightTilePoint(PlayerInfo $playerInfo)
    {
        if ($playerInfo->x === $this->maxX) {
            return null;
        }

        return [$playerInfo->x + 1, $playerInfo->y];
    }

    protected function getUpTilePoint(PlayerInfo $playerInfo)
    {
        if ($playerInfo->y === 0) {
            return null;
        }

        return [$playerInfo->x, $playerInfo->y - 1];
    }

    protected function getDownTilePoint(PlayerInfo $playerInfo)
    {
        if ($playerInfo->y === $this->maxY) {
            return null;
        }

        return [$playerInfo->x, $playerInfo->y + 1];
    }
}
