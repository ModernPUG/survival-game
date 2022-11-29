# 2022년 모던 PHP 유저 그룹 송년회 게임

## 게임 안내

각자 자신의 플레이어 클래스를 개발하여 참여하는 게임입니다.
각 플레이어 클래스 알고리즘에 따라 플레이어는 게임 맵을 돌아다닙니다.
맵에서는 랜덤으로 폭발이 발생하고 폭발에 당한 플레이어는 HP가 줄어들고 HP가 0이 되면 탈락합니다.
보호막을 먹으면 폭발에 당할 시 HP 대신 보호막이 줄어듭니다.
마지막까지 살아남는 플레이어가 승리합니다.

아래 스크린샷을 클릭하여 영상으로 확인해보세요.
[![2022년 MPUG 송년회 게임 트레일러](https://img.youtube.com/vi/EJ7i_nKE7Ag/maxresdefault.jpg)](https://youtu.be/EJ7i_nKE7Ag)

## 참여 방법

코딩을 하지 않아도 참여할 수 있습니다. 자세한 내용은 아래 링크를 확인해주세요.

> [코딩 없는 간단한 참여 방법 바로가기](https://blog.naver.com/modernpug/222934394409)

1. 이 저장소를 Fork 하여 내 저장소를 만듭니다.
2. lib/Users 디렉토리에 __{MyUniqueName}.php__ 로 클래스 파일을 만듭니다.
3. web/img/users 디렉토리에 내 플레이어 이미지 파일을 넣습니다.
   - 파일 포맷 : PNG
   - 파일 이름 : __{클래스명과 동일}.png__
   - 이미지 크기 : 가로세로 192px 이하 정사각형
4. lib/Users/SampleUser.php 파일의 예제 코드를 참고하여 나만의 코드를 작성합니다.
5. 내 저장소에 커밋 후 __event2022 브랜치__ 로 보내는 Pull Request를 생성합니다.
6. MPUG 운영진이 코드를 검토 후 병합합니다.
7. 경품 당첨 시 __GitHub 프로필에 등록된 이메일__ 로 연락드립니다.

## 실행 방법

composer와 npm(또는 yarn), docker-compose가 필요합니다.
터미널에서 아래의 과정을 진행합니다.

1. PHP 패키지 설치

    ```shell
    composer install
    ```

2. JS 패키지 설치

    ```shell
    npm install
    ```

    또는

    ```shell
    yarn install
    ```

3. 도커 컨테이너 실행

    ```shell
    docker-compose up -d
    ```

4. 웹브라우저에서 https://localhost:8443 접속
이때 SSL 인증서 오류는 무시합니다.

## 클래스 코딩 안내

- PhpStorm, VSCode 등의 PHP를 지원하는 IDE나 편집기를 사용하세요.

- SampleUser 클래스 처럼 랜덤 이동으로 코딩해도 괜찮습니다.

- 플레이어가 이동하려는 타일에 다른 플레이어가 존재하면 이동이 불가능합니다.

    ```php
    /*
     * 특정 x,y 위치에 다른 플레이어가 있는지 확인하는 코드
     */

    $tile_info = $tile_info_table[$y][$x];

    if ($tile_info->exist_player) {
        echo '있다';
    } else {
        echo '없다';
    }
    ```

- 방어막이 있는 타일에 플레이어가 있으면 폭발 공격을 받지 않습니다.

    ```php
    /*
     * 특정 x,y 위치에 방어막이 있는지 확인하는 코드
     */

    $tile_info = $tile_info_table[$y][$x];

    if ($tile_info->exist_shield) {
        echo '있다';
    } else {
        echo '없다';
    }
    ```

- Exception이 발생하면 플레이어는 이동하지 않습니다.

- 게임에 영향을 주거나 시스템에 위험한 행위를 시도하는 코드는 허가되지 않습니다.
