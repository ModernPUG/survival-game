<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class TerrorboyUser implements \App\UserInterface
{
    private string $ment = '';

    /**
     * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Terrorboy';
    }

    /**
     * 게임 화면에 표시될 플레이어 메시지입니다.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->ment;
    }

    /**
     * ! 사용자 액션
     *
     * @param \App\PlayerInfo $player_info 플레이어 정보
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return \App\ActionEnum
     */
    public function action(\App\PlayerInfo $player_info, array $tile_info_table): ActionEnum
    {
        if ($player_info->hp <= 1) {
            if (($player_info->shield??0) > 0) {
                $this->ment = '아직 '.($player_info->shield??0).'발 남았다';
            } else {
                $this->ment = '아직은 살아있는중...';
            }
        } else {
            $ment = [
                '(드립실패)',
                '여긴어디? 난 누구?',
                '피바람이 부는구나!!',
                '히익!!!!!!!!',
                '살려주세요 ㅠ.ㅠ;',
                '허허...',
                '항암',
                '도사야 알지?',
            ];
            shuffle($ment);
            $this->ment = $ment[0]??'';
        }
        return ActionEnum::Hold;
        $move = $this->movePoint($player_info, $tile_info_table);
        $this->preview($player_info, $tile_info_table);
        return $move;

        //Game::BOOM_TURNS; // 해당 턴 수 마다 폭발이 발생 합니다.
        //Game::mapColNum(); // 맵의 가로 개수
        //Game::mapRowNum(); // 맵의 세로 개수

        $player_info->x; // 플레이어 가로 위치
        $player_info->y; // 플레이어 세로 위치
        $player_info->hp; // 플레이어 HP
        $player_info->shield; // 플레이어 보호막

        // 2차원 배열 타일 정보 전체 확인
        foreach ($tile_info_table as $y => $tile_info_rows) {
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

    /**
     * ! 이동위치 결정
     *
     * (현재는 쉴드가 터진 자리는 피해다닌다. - 테스트 해보니 쉴드 터진자리에 다시 터질 확률이 높다 위선 터진 자리로 이동하도록 한다.)
     *
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return null|int
     */
    private function movePoint(\App\PlayerInfo $player_info, array $tile_info_table)
    {

        // 기본변수
        $px = ($player_info->x??0); // 플레이어의 X 위치
        $py = ($player_info->y??0); // 플레이어의 Y 위치
        $mx = (Game::mapColNum()??10); // 맵의 최대 X
        $my = (Game::mapRowNum()??10); // 맵의 최대 Y
        $around = []; // 내주변 이벤트 배열

        dd($px, $py, $mx, $my);
        // TODO: 가장 가까운 쉴드 찾기

        // 내주변 이벤트
        $point = ($py*$mx)+$px; // 플래이어의 현재 위치
        /*
            // ! 추후 업데이트 되면 사용
            $left = $point-1;
            $right = $point+1;
            $top = $point-$mx;
            $bottom = $point+$mx;
        */
        if ($px > 0 ) {
            $eventPoint = $titleInfoTable[$py][$px-1];
            if (empty($eventPoint->exist_shield)) { // 추후 업데이트 되면 조건 추가 (붐 여부)
                $around[] = 2;
            }
        }
        if ($px < ($mx-1)) {
            $eventPoint = $titleInfoTable[$py][$px+1];
            if (!empty($eventPoint->exist_shield)) { // 추후 업데이트 되면 조건 추가 (붐 여부)
                $around[] = 3;
            }
        }
        if ($py > 0) {
            $eventPoint = $titleInfoTable[$py-1][$px];
            if (!empty($eventPoint->exist_shield)) { // 추후 업데이트 되면 조건 추가 (붐 여부)
                $around[] = 0;
            }
        }
        if ($py < ($my-1)) {
            $eventPoint = $titleInfoTable[$py+1][$px];
            if (!empty($eventPoint->exist_shield)) { // 추후 업데이트 되면 조건 추가 (붐 여부)
                $around[] = 1;
            }
        }

        // 플레이어 위치 미리보기
        //$this->preview($mx, $my, $px, $py);

        if (count($around) > 0) {
            return array_rand(array_flip($around));
        } else {
            //return null; // null반환시 enum에서 지원하지 않기 때문에 오류로 내위치가 고정된다. - 쉴드 터진자리 피해 다닐때
            return mt_rand(0, 3); // 쉴드 터진 자리를 찾을 때까지 랜덤 이동
        }
    }

    /**
     * * 플레이어 위치 미리보기
     *
     * @return string
     */
    private function preview(\App\PlayerInfo $player_info, array $tile_info_table)
    {
        // 테스트를 위해 쉴드 고정
        $tile_info_table[0][6] = true;

        for ($y=0; $y<(Game::mapRowNum()??10); $y++) {
            if ($y > 0) {
                echo '<br>';
            }
            for ($x=0; $x<(Game::mapColNum()??10); $x++) {
                $shield = ($tile_info_table[$y][$x]??false);
                if ($shield === true) {
                    if ($player_info->x == $x && $player_info->y == $y) {
                        echo '✣';
                    } else {
                        echo '🛡️';
                    }
                }
                if ($player_info->x == $x && $player_info->y == $y) {
                    echo '◼︎';
                } else {
                    echo '﹒';
                }
            }
        }
        exit;
    }
}
