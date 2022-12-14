<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/* 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class jay implements \App\UserInterface
{
    private string $ment = '';
    private string $playerName = 'jay';

    /* 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->playerName;
    }

    /* 게임 화면에 표시될 플레이어 메시지입니다.
     * @return string
     */
    public function getMessage(): string
    {
        return $this->ment;
    }

    public function checkNegative($value){
        if ($value < 0){
            $value = $value * -1;
        }
        return $value;
    }

    /* * 사용자 액션
     *
     * @param \App\PlayerInfo $player_info 플레이어 정보
     * @param \App\TileInfo[][] $tile_info_table [세로y][가로x] 2차원 배열에 담긴 타일 정보
     * @return \App\ActionEnum
     */
    public function action(\App\PlayerInfo $player_info, array $tile_info_table): ActionEnum
    {

        $player_info->x; // 플레이어 가로 위치
        $player_info->y; // 플레이어 세로 위치
        $player_info->hp; // 플레이어 HP
        $player_info->shield; // 플레이어 보호막

        $shieldArray=array();
        $distanceSort =array();
        $index = 0;

        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** @var \App\TileInfo $tile_info */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
                $distanceX = $this->checkNegative($x - $player_info->x);
                $distanceY = $this->checkNegative($y - $player_info->y);
                $distance = $distanceX+ $distanceY;
                
                if ($tile_info->exist_shield == 1){
                    
                    $distanceSort[$index] = $distance;
                    $shieldArray[$index] = array(
                        'x' => $x,
                        'y' => $y,
                        'distance' => $distance

                    );
                    $index++;
                }
                
            }
          }
    
        asort($distanceSort);
        $firstKey = array_key_first($distanceSort);
        $shieldX = $shieldArray[$firstKey]['x'];
        $shieldY = $shieldArray[$firstKey]['y'];
        $shieldD = $shieldArray[$firstKey]['distance'];
        $distansY = $shieldY - $player_info->y;
        $distansX = $shieldX - $player_info->x;

        if ($distansX == 0 ){
                
            if( $distansY > 0){
                $i = 2;
            } else if( $distansY < 0){
                $i = 1;
            } else {
                $i = 0;
            };

        }
        else if($distansX < 0){
            $i = 3;
        } else if($distansX > 0){
            $i = 4;
        } else{
            $i = rand(0,4);
        };

        //$this->ment ="(".$shieldX.",".$shieldY.") ".$shieldD.",".$distansX.",".$distansY." move:".$i. " shield:".$player_info->shield;
        $message = array(
            "네 보호막은 내 보호막",
            "어디 한번 이겨볼까!",
            "긴장해!",
            "이제 봐주지 않아!!"
        );

        $m = rand(0,3);
        $this->ment = $message[$m];

        if ($player_info->hp > 6){
            $i = rand(0,4);
            $message = array(
                "청소중",
                "귀찮네",
                "졸려",
                "산책해볼까?",
                "자유롭게!"
            );
            $this->ment = $message[$i];
        }
        
        return match ($i) {
            0 => ActionEnum::Hold,
            1 => ActionEnum::Up,
            2 => ActionEnum::Down,
            3 => ActionEnum::Left,
            4 => ActionEnum::Right,
        };
    }


}
