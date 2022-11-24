<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class Getsolaris implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'getsolaris';
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
        return ActionEnum::Down;
    }
}
