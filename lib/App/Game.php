<?php

declare(strict_types=1);

namespace App;

class Game
{
    /** 방어막 위치 변경 턴 수 */
    public const SHIELD_TURNS = 5;

    /** 폭발 발생 턴 수 */
    public const BOOM_TURNS = 3;

    /** @var \SplObjectStorage<Player> */
    private \SplObjectStorage $player_list;

    /** @var Pos[] */
    private array $pos_list = [];

    private array $play_data_list_log = [];

    private readonly Map $map;

    public function __construct(
        public readonly int $col_num,
        public readonly int $row_num,
    ) {
        $map = new Map($this->col_num, $this->row_num);
        $player_list = new \SplObjectStorage();

        $dir_it = new \DirectoryIterator(__DIR__ . '/../Users');
        foreach ($dir_it as $fileinfo) {
            if (
                $fileinfo->isDot()
                || $fileinfo->getType() != 'file'
                || $fileinfo->getExtension() != 'php'
            ) {
                continue;
            }

            $filename = $fileinfo->getFilename();
            $classname = 'Users\\' . preg_replace('/\.php$/', '', $filename);

            $class = new \ReflectionClass($classname);
            $result = $class->implementsInterface('App\UserInterface');
            if (!$result) {
                continue;
            }

            $user = $class->newInstance();
            $player = new Player($map, $user);
            $player_list->attach($player);
            $map->addPlayer($player);
        }

        $this->map = $map;
        $this->player_list = $player_list;

        for ($y = 0; $y < $this->row_num; $y++) {
            for ($x = 0; $x < $this->col_num; $x++) {
                $this->pos_list[] = new Pos($x, $y);
            }
        }
    }

    private function getPlayerDataList(): array
    {
        $player_info_list = [];
        foreach ($this->player_list as $player) {
            $player_info_list[] = $player->getInfo();
        }
        return $player_info_list;
    }

    private function makeShieldDataList(): array
    {
        $count = (int)ceil(($this->col_num * $this->row_num) / 10);
        $rand_key_list = array_rand($this->pos_list, $count);

        $shield_data_list = array_map(function (int $key) {
            $pos = $this->pos_list[$key];
            return [
                'type' => 'shield',
                'x' => $pos->x,
                'y' => $pos->y,
            ];
        }, $rand_key_list);

        return $shield_data_list;
    }

    private function makeBoomDataList(): array
    {
        $count = (int)ceil(($this->col_num * $this->row_num) / 2);
        $rand_key_list = array_rand($this->pos_list, $count);

        $boom_data_list = array_map(function (int $key) {
            $pos = $this->pos_list[$key];
            return [
                'type' => 'boom',
                'x' => $pos->x,
                'y' => $pos->y,
            ];
        }, $rand_key_list);

        return $boom_data_list;
    }

    private function logPlayDataList(array $play_data_list): void
    {
        $this->play_data_list_log[] = $play_data_list;
    }

    public function play(): void
    {
        $this->logPlayDataList($this->getPlayerDataList());
        $shield_data_list = [];

        for ($i = 1; $i <= 100; $i++) {
            $suffle_player_list = iterator_to_array($this->player_list);
            shuffle($suffle_player_list);
            foreach ($suffle_player_list as $player) {
                try {
                    @$player->action();
                } catch (\Throwable $th) {
                    // 모든 오류 무시
                }
            }

            $this->logPlayDataList($this->getPlayerDataList());

            if ($i % self::SHIELD_TURNS == 0) {
                $this->map->resetAllShield();
                $this->logPlayDataList([['type' => 'reset_shield']]);

                $shield_data_list = $this->makeShieldDataList();
                $this->logPlayDataList($shield_data_list);

                foreach ($shield_data_list as $shield_data) {
                    $pos = new Pos($shield_data['x'], $shield_data['y']);
                    $this->map->addShield($pos);
                }
            }

            if ($i % self::BOOM_TURNS == 0) {
                $boom_data_list = $this->makeBoomDataList();
                $this->logPlayDataList($boom_data_list);
            } else {
                $boom_data_list = [];
            }

            foreach ($boom_data_list as $boom_data) {
                $pos = new Pos($boom_data['x'], $boom_data['y']);

                $result = $this->map->hasShieldByPos($pos);
                if ($result) {
                    continue;
                }

                $player = $this->map->getPlayerByPos($pos);
                if ($player) {
                    $player->damage();
                }
            }

            $this->logPlayDataList($this->getPlayerDataList());

            $survivor_count = 0;
            foreach ($this->player_list as $player) {
                $survivor_count += $player->getHp() > 0 ? 1 : 0;
            }

            if ($survivor_count < 2) {
                break;
            }
        }
    }

    public function getPlayDataListLog(): array
    {
        return $this->play_data_list_log;
    }
}
