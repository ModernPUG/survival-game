<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class TerrorboyUser implements \App\UserInterface
{
    private array $playerInfo;
    private array $playInfo;

    /**
     * * 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Terrorboy';
    }

    /**
     * * 사용자 액션
     *
     * @param integer $playerX 플레이어 가로 위치
     * @param integer $playerY 레이어 세로 위치
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return \App\ActionEnum
     */
    public function action(int $playerX, int $playerY, array $titleInfoTable): ActionEnum
    {
        // 처리가 쉽도록
        $this->playerInfo = [
            'x'=>$playerX,
            'y'=>$playerY,
        ];
        $this->playInfo = [
            'max_x'=>count($titleInfoTable[0]??[]),
            'max_y'=>count($titleInfoTable??[]),
        ];

        // 이동 할곳을 찾는다.
        $move = $this->movePoint($titleInfoTable);

        // 이동
        return match ($move) {
            0 => ActionEnum::Up,
            1 => ActionEnum::Down,
            2 => ActionEnum::Left,
            3 => ActionEnum::Right,
        };
    }

    /**
     * * 내주변 이벤트 조건에 따라 이동위치를 반환한다.
     *
     * @todo 내주변에 BOOM이 터졌다면 해당 자리로 이동한다.
     * (현재는 쉴드가 터진 자리는 피해다닌다. - 테스트 해보니 쉴드 터진자리에 다시 터질 확률이 높다 위선 터진 자리로 이동하도록 한다.)
     * 
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return null|int
     */
    private function movePoint(array $titleInfoTable)
    {
        // 기본변수
        $px = ($this->playerInfo['x']??0); // 플레이어의 X 위치
        $py = ($this->playerInfo['y']??0); // 플레이어의 Y 위치
        $mx = ($this->playInfo['max_x']??0); // 맵의 최대 X
        $my = ($this->playInfo['max_y']??0); // 맵의 최대 Y
        $around = []; // 내주변 이벤트 배열

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
     * 플레이어 위치 미리보기
     *
     * @param integer $maxX : 맵의 최대 X
     * @param integer $maxY : 맵의 최대 Y
     * @param integer $playerX : 플레이어의 X 위치
     * @param integer $playerY : 플레이어의 Y 위치
     * @return string
     */
    private function preview(int $maxX, int $maxY, int $playerX, int $playerY)
    {
        for ($y=0; $y<$maxY; $y++) {
            if ($y > 0) {
                echo ($y-1).'<br>';
            }
            for ($x=0; $x<$maxX; $x++) {
                if ($playerX == $x && $playerY == $y) {
                    echo '◼︎';
                } else {
                    echo '☐';
                }
            }
        }
    }
}
