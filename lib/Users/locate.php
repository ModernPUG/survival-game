<?php

declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;

/**
 * 본인이 개발한 클래스에 대한 소개를 주석에 자유롭게 작성해주세요.
 * 이 예제 코드를 참고하여 본인만의 클래스를 만들어주세요.
 */
class locate implements \App\UserInterface
{
    private string $ment = '';
    private string $playerName = '땅바닥';

    /** 게임 화면에 표시될 플레이어 이름입니다.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->playerName;
    }

    /** 케릭터 이름
     * @return string
     */
    public function getMessage(): string
    {
        return $this->ment;
    }

    /** 절대값 구하기
     *
     */
    public function absoluteValue($value){
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

        Game::BOOM_TURNS; // 해당 턴 수 마다 폭발이 발생 합니다.
        Game::mapColNum(); // 맵의 가로 개수
        Game::mapRowNum(); // 맵의 세로 개수

        $player_info->x; // 플레이어 가로 위치
        $player_info->y; // 플레이어 세로 위치
        $player_info->hp; // 플레이어 HP
        $player_info->shield; // 플레이어 보호막

        $shieldArray = array(); //  방어막 좌표 리스트
        $shieldDistanceArray = array(); // 방어막 거리 배열
        $shieldIndex = 0;

        $playerArray = array(); //  방어막 좌표 리스트
        $playerDistanceArray = array(); // 방어막 거리 배열
        $playerIndex = 0;
        foreach ($tile_info_table as $y => $tile_info_rows) {
            /** 
             * @var \App\TileInfo $tile_info 
             * */
            foreach ($tile_info_rows as $x => $tile_info) {
                $x; // 가로 위치
                $y; // 세로 위치
                $tile_info->exist_player; // 플레이어 존재 여부
                $tile_info->exist_shield; // 방어막 존재 여부
                $distanceX = $this->absoluteValue($x - $player_info->x); // 방어막까지의 가로 거리
                $distanceY = $this->absoluteValue($y - $player_info->y); // 방어막까지의 세로 거리
                $distance = $distanceX+ $distanceY;
                
                // 방어막 Array 
                if ($tile_info->exist_shield == 1) {

                    // 가장 가까운 거리의 방어막을 찾기 위해 방어막의 거리 배열 생성
                    $shieldDistanceArray[$shieldIndex] = $distance;
                    $shieldArray[$shieldIndex] = array(
                        'x' => $x,
                        'y' => $y,
                        'distance' => $distance
                    );
                    $shieldIndex++;

                }

                /** 플레이어 Array 
                 * 가장 가까운 것은 플레이어 자신임으로 타 플레이어는 거리가 0이상 차이 나야함
                 */
                if ($tile_info->exist_player == 1 && $distance > 0) {

                    // 가장 가까운 거리의 방어막을 찾기 위해 방어막의 거리 배열 생성
                    $playerDistanceArray[$playerIndex] = $distance;
                    
                    $playerArray[$playerIndex] = array(
                        'x' => $x,
                        'y' => $y,
                        'distance' => $distance
                        
                    );
                    $playerIndex++;

                }
                
            }
          }
    
        // 방어막 거리 배얼을 거리순으로 정렬
        asort($shieldDistanceArray);
        asort($playerDistanceArray);

        // 가장 가까운 거리의 key를 가져옴
        $sheildNearestKey= array_key_first($shieldDistanceArray);
        $playerNearestKey= array_key_first($playerDistanceArray);

        // 가장 가까운 거리의 방어막 좌표
        $shieldX = $shieldArray[$sheildNearestKey]['x'];
        $shieldY = $shieldArray[$sheildNearestKey]['y'];

        // 가장 가까운 거리의 방어막 까지의 거리
        $shieldDistansX = $shieldX - $player_info->x;
        $shieldDistansY = $shieldY - $player_info->y; 

        // 가장 가까운 거리의 플레이어 좌표
        $playerX = $playerArray[$playerNearestKey]['x'];
        $playerY = $playerArray[$playerNearestKey]['y'];

        // 가장 가까운 거리의 플레이어 까지의 거리
        $playerDistansX = $playerX - $player_info->x;
        $playerDistansY = $playerY - $player_info->y;
        $playerDistance = $this->absoluteValue($playerDistansX)+$this->absoluteValue($playerDistansY);

        // 가장 가까운 방어막으로 이동
        
        /** 플레이어와의 거리가 2차이가 나는지 확인
         * 차이가 나지 않으면 그대로 진행
         * 차이가 나면 X, Y 비교
         * */
        if ($playerDistance > 1){
            
            if ($this->absoluteValue($shieldDistansX) > $this->absoluteValue($playerDistansY)){
                // X축 이동
                if($shieldDistansX < 0){
                    $i = 3;
                } else {
                    $i = 4;
                }
                /*else{
                    $i = rand(0,4);
                };*/
            } else if ($this->absoluteValue($shieldDistansX) < $this->absoluteValue($playerDistansY)){

                // Y축 이동
                if( $shieldDistansY > 0){
                    $i = 2;
                } else {
                    $i = 1;
                }

            } else {

                if ($shieldDistansX == 0 ){
                        
                    // Y축 이동
                    if( $shieldDistansY > 0){
                        $i = 2;
                    } else {
                        $i = 1;
                    }
                } else {

                    // X축 이동
                    if($shieldDistansX < 0){
                        $i = 3;
                    } else {
                        $i = 4;
                    }
                }

            }
            

        } else if ($this->absoluteValue($playerDistansX) > $this->absoluteValue($playerDistansY)){

            // X축 이동
            if($shieldDistansX < 0){
                $i = 3;
            } else {
                $i = 4;
            }

        } else{

            // Y축 이동
            if( $shieldDistansY > 0){
                $i = 2;
            } else {
                $i = 1;
            }
        }


        //$this->ment ="(".$playerX.",".$playerY.") ".$nearestKey.",".$distansX.",".$distansY." move:".$i. " shield:".$player_info->shield;
        /*$message = array(
            "네 보호막은 내 보호막",
            "어디 한번 이겨볼까!",
            "긴장해!",
            "이제 봐주지 않아!!"
        );

        $m = rand(0,3);
        $this->ment = $message[$m];
        */
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
