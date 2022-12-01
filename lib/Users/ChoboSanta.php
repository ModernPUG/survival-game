<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 바람의나라 "산타옷"을 입은 케릭터 입니다
 */
class ChoboSanta implements \App\UserInterface
{
    // 플레이어 상태
    private \App\PlayerInfo $player;
    // '이동 가능한' 쉴드좌표
    private array $shields = [];

    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return '초보산타';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        $msg_list = [
            '나는 산타!',
            '다람쥐를 뿌려려!',
            '토도리 다판다!',
            '해피 크리스마스!',
            '나는 빡빡이다!',
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

        $this->resetShield();

        // 플레이어 상태 기록
        $this->setPlayer($player_info);

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
                
                // 쉴드 정보 기록
                $this->setShield([
                    'x' => $x,
                    'y' => $y,
                    'exist_player' => $tile_info->exist_player,
                    'exist_shield' => $tile_info->exist_shield,
                ]);
            }
        }

        return match ($this->movingShieldTile()) {
            0 => ActionEnum::Hold,
            1 => ActionEnum::Up,
            2 => ActionEnum::Down,
            3 => ActionEnum::Left,
            4 => ActionEnum::Right,
        };
    }

    private function setPlayer(\App\PlayerInfo $player_info): void
    {
        $this->player = $player_info;
    }

    private function setShield(array $shield): void
    {
        if ($this->isSafeTile($shield) === true) {
            $this->shields[] = [
                'x' => $shield['x'],
                'y' => $shield['y'],
            ];
        }
    }

    // 이동 가능한 안전한 타일 좌표만 저장
    private function isSafeTile(array $shield): bool
    {
        $safe = false;

        if (
            // 타일에 다른 케릭터 X
            $shield['exist_player'] === false &&
            // 쉴드가 존재
            $shield['exist_shield'] === true
        ) {
            $safe = true;
        }

        return $safe;
    }

    private function resetShield(): void
    {
        $this->shields = [];
    }

    // 쉴드 좌표로 이동
    private function movingShieldTile(): int
    {
        $shieldCoor = $this->shortShieldDistance();

        return $this->direction($shieldCoor);
    }

    /**
     * 가장 가까운 쉴드 좌표 구하기
     * 
     * 알고리즘 잘 몰라서 생각나는대로 구현했습니다..
     */
    private function shortShieldDistance(): array
    {
        // 게임 초반엔 데이터가 없다
        if (
            count($this->shields) === 0 ||
            isset($this->player) === false
        ) {
            return [
                'x' => 0,
                'y' => 0,
            ];
        }

        $distanceList = [];

        // 쉴드가 존재하는 모든 필드중 가까운곳 찾기
        foreach($this->shields as $coord) {
            // 내 케릭터 기준으로 좌표거리 구하기
            $distanceX = $this->player->x - $coord['x'];
            $distanceY = $this->player->y - $coord['y'];

            // 내 케릭터 기준으로 가까운거리 구하기
            $distance = abs($distanceX) + abs($distanceY);

            // x,y 좌표 무지성으로 등록!
            $distanceList[$distance] = [
                'x' => $distanceX,
                'y' => $distanceY,
            ];
        }

        // 오름차순 키 정렬
        ksort($distanceList);

        // 가까운 쉴드 좌표 1개 반환
        return current($distanceList);
    }

    private function direction(array $shieldCoor): int
    {
        $direction = 0;

        // x 좌표 먼저 이동
        if ($shieldCoor['x'] !== 0) {
            $direction = $this->convertX($shieldCoor['x']);
        } else {
            $direction = $this->convertY($shieldCoor['y']);
        }
        
        return $direction;
    }

    /**
     * 양수: 왼쪽(3) 이동
     * 음수: 오른쪽(4) 이동
     */
    private function convertX(int $coorX): int
    {
        if ($coorX === 0) {
            return $coorX;
        }
        
        return ($coorX > 0) ? 3 : 4;
    }

    /**
     * 양수: 위(1) 이동 
     * 음수: 아래(2) 이동
     */
    private function convertY(int $coorY): int
    {
        if ($coorY === 0) {
            return $coorY;
        }

        return ($coorY > 0) ? 1 : 2;
    }

}