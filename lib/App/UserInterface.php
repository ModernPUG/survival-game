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
     * 플레이어 메시지
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * 플레이어 액션
     *
     * @param PlayerInfo $player_info 플레이어 정보
     * @param TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return ActionEnum
     */
    public function action(PlayerInfo $player_info, array $tile_info_table): ActionEnum;
}
