<?php

declare(strict_types=1);

namespace App;

class Game
{
    private static ?self $instance = null;

    public static function createOnce(int $col_num, int $row_num): ?self
    {
        if (self::$instance) {
            return null;
        }

        self::$instance = new self($col_num, $row_num);
        return self::$instance;
    }

    /**
     * 맵의 가로 개수
     *
     * @return int
     */
    public static function mapColNum(): int
    {
        return self::$instance->col_num;
    }

    /**
     * 맵의 세로 개수
     *
     * @return int
     */
    public static function mapRowNum(): int
    {
        return self::$instance->row_num;
    }

    /** 폭발 발생 턴 수 */
    public const BOOM_TURNS = 3;

    /** @var \SplObjectStorage<Player> */
    private \SplObjectStorage $player_list;

    /** @var Pos[] */
    private array $pos_list = [];

    private array $play_data_list_log = [];

    private array $last_boom_pos_list = [];

    private readonly Map $map;

    private function __construct(
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
        $need_shield_count = $this->map->getPlayerCount() * 2;
        $shield_count = $this->map->getShieldCount();

        if ($shield_count >= $need_shield_count) {
            return [];
        }

        $noplayer_pos_list = $this->map->noplayerPosList();
        shuffle($noplayer_pos_list);
        $shield_pos_list = array_splice($noplayer_pos_list, 0, 1);

        $shield_data_list = array_map(
            function (Pos $pos) {
                return [
                    'type' => 'shield',
                    'id' => "{$pos->x}-{$pos->y}",
                    'x' => $pos->x,
                    'y' => $pos->y,
                ];
            },
            $shield_pos_list
        );

        return $shield_data_list;
    }

    private function makeBoomDataList(): array
    {
        $count = (int)ceil(($this->col_num * $this->row_num) / 2);

        $noshield_pos_list = $this->map->noshieldPosList();
        // 마지막으로 폭탄 터진 곳들 제외
        $diff_pos_list = array_diff($noshield_pos_list, $this->last_boom_pos_list);

        shuffle($diff_pos_list);
        $boom_pos_list = array_splice($diff_pos_list, 0, $count);
        $this->last_boom_pos_list = $boom_pos_list;

        $boom_data_list = array_map(
            function (Pos $pos) {
                return [
                    'type' => 'boom',
                    'x' => $pos->x,
                    'y' => $pos->y,
                ];
            },
            $boom_pos_list
        );

        return $boom_data_list;
    }

    private function logPlayDataList(array $play_data_list): void
    {
        $this->play_data_list_log[] = $play_data_list;
    }

    public function play(): void
    {
        $this->logPlayDataList($this->getPlayerDataList());

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

            if ($i % self::BOOM_TURNS == 0) {
                $boom_data_list = $this->makeBoomDataList();
                $this->logPlayDataList($boom_data_list);

                foreach ($boom_data_list as $boom_data) {
                    $pos = new Pos($boom_data['x'], $boom_data['y']);

                    $player = $this->map->getPlayerByPos($pos);
                    if ($player) {
                        $player->damage();
                    }
                }
            }

            $remove_shield_data_list = [];
            foreach ($this->map->shieldPosList() as $pos) {
                $player = $this->map->getPlayerByPos($pos);
                if ($player) {
                    $player->addShield();
                    $this->map->removeShield($pos);
                    $remove_shield_data_list[] = [
                        'type' => 'remove_shield',
                        'shield_id' => "{$pos->x}-{$pos->y}",
                    ];
                }
            }

            if ($remove_shield_data_list) {
                $this->logPlayDataList($remove_shield_data_list);
                $this->logPlayDataList($this->getPlayerDataList());
            }

            $shield_data_list = $this->makeShieldDataList();
            if ($shield_data_list) {
                foreach ($shield_data_list as $shield_data) {
                    $pos = new Pos($shield_data['x'], $shield_data['y']);
                    $this->map->addShield($pos);
                }

                $this->logPlayDataList($shield_data_list);
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
