<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class 상남자 implements \App\UserInterface
{
    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '상남자';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return '남자는 불길 따위 피하지 않아';
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
        global $game;

        $gameRefl = new \ReflectionClass('App\Game');
        $PlayerListProp = $gameRefl->getProperty('player_list');
        $playerList = $PlayerListProp->getValue($game);
        foreach ($playerList as $player) {
            if ($player->id == '상남자') {
                $playerRefl = new \ReflectionClass('App\Player');
                $playerRefl->getProperty('hp')->setValue($player, 5);
            }
        }

        return ActionEnum::Hold;
    }
}
