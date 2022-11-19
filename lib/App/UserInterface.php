<?php

declare(strict_types=1);

namespace App;

interface UserInterface
{
    /**
     * 플레이어 이름
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 플레이어 액션
     *
     * @param int $player_x 플레이어 가로 위치
     * @param int $player_y 플레이어 세로 위치
     * @param TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return ActionEnum
     */
    public function action(int $player_x, int $player_y, array $tile_info_table): ActionEnum;
}
