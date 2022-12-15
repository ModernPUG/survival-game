<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;
use App\Pos;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class RedHelmet implements \App\UserInterface
{
    private int $last_hp = 0;
    private int $last_shield = 0;

    private MessageFlag $message_flag = MessageFlag::Normal;

    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '붉은투구';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = match ($this->message_flag) {
            MessageFlag::Normal => [
                '화공을 조심하라~!',
                '적이 화계를 준비하고 있다는 첩보다.',
                '진격!!',
                '주위를 살펴라~!',
                '자신의 자리를 지켜라~!',
                '끝까지 살아남아야 한다.',
                '적의 기습에 대비하라~'
            ],
            MessageFlag::Victory => [
                '승리가 눈앞에 있다.',
                '마지막 까지 적의 공격에 대비하라.',
                '여기서 살아남는자가 승리한다.',
                '이제 거의 다왔다.'
            ],
            MessageFlag::Boom => [
                '크헉.. 이런! 방심했다...',
                '모두 불을 진화하라!',
                '이런.. 피해가 너무 크다..'
            ],
            MessageFlag::LowHp => [
                '아군의 숫자가 너무 적다..',
                '큰일이다. 체력이 고갈되어 간다.',
                '여기까지란 말인가....'
            ]
        };

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

        // 메시지 놀이
        $this->message_flag = MessageFlag::Normal;

        // - HP가 얼마 남지 않았을 때
        if ($player_info->hp <= 2) {
            $this->message_flag = MessageFlag::LowHp;
        }

        // - 플레이어의 숫자가 3명 이하인경우 승리를 기대
        if ($this->getPlayerCount($tile_info_table) <= 3) {
            $this->message_flag = MessageFlag::Victory;
        }

        // - 이전 턴 보다 체력이나 실드가 줄어들었으면 폭탄이 터진것으로 간주
        if ($this->last_hp > $player_info->hp || $this->last_shield > $player_info->shield) {
            $this->message_flag = MessageFlag::Boom;
        }
        $this->last_hp = $player_info->hp;
        $this->last_shield = $player_info->shield;


        // 가장 가까운 실드의 위치를 구한다.
        $near_shield = $this->getNearShieldPos($player_info, $tile_info_table);
        if ($near_shield === null) {
            // 가까운 실드가 없으면 경우 랜덤
            $i = mt_rand(0, 4);
            return match ($i) {
                0 => ActionEnum::Hold,
                1 => ActionEnum::Up,
                2 => ActionEnum::Down,
                3 => ActionEnum::Left,
                4 => ActionEnum::Right,
            };
        }

        // 상하좌우에 플레이어 존재여부 체크
        $is_exsit_up = ($player_info->y - 1) > 0
            ? $tile_info_table[$player_info->y - 1][$player_info->x]->exist_player
            : false;
        $is_exsit_down = ($player_info->y + 1) < Game::mapRowNum()
            ? $tile_info_table[$player_info->y + 1][$player_info->x]->exist_player
            : false;
        $is_exsit_left = ($player_info->x - 1) > 0
            ? $tile_info_table[$player_info->y][$player_info->x - 1]->exist_player
            : false;
        $is_exsit_right = ($player_info->x + 1) < Game::mapColNum()
            ? $tile_info_table[$player_info->y][$player_info->x + 1]->exist_player
            : false;

        // 가까운 실드를 향해서 플레이어가 없는 방향으로 진행
        if (!$is_exsit_left && $player_info->x > $near_shield->x) {
            return ActionEnum::Left;
        } else if (!$is_exsit_right && $player_info->x < $near_shield->x) {
            return ActionEnum::Right;
        } else if (!$is_exsit_up && $player_info->y > $near_shield->y) {
            return ActionEnum::Up;
        } else if (!$is_exsit_down && $player_info->y < $near_shield->y) {
            return ActionEnum::Down;
        }

        // 
        return ActionEnum::Hold;
    }

    private function getNearShieldPos(\App\PlayerInfo $player_info, array $tile_info_table): Pos|null
    {
        $near_shield = null;
        $near_shield_distance = -1;
        foreach ($tile_info_table as $y => $tile_info_rows) {
            foreach ($tile_info_rows as $x => $tile_info) {
                if ($tile_info->exist_shield) {
                    $distance = abs($x - $player_info->x) + abs($y - $player_info->y);
                    if ($near_shield_distance === -1 || $distance < $near_shield_distance) {
                        $near_shield_distance = $distance;
                        $near_shield = new Pos($x, $y);
                    }
                }
            }
        }

        return $near_shield;
    }

    private function getPlayerCount(array $tile_info_table): int
    {
        $count = 0;
        foreach ($tile_info_table as $y => $tile_info_rows) {
            foreach ($tile_info_rows as $x => $tile_info) {
                if ($tile_info->exist_player) {
                    $count++;
                }
            }
        }

        return $count;
    }
}

enum MessageFlag
{
    case Normal;
    case Boom;
    case Victory;
    case LowHp;
}
