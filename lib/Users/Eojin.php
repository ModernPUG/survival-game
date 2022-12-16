<?php declare(strict_types=1);

namespace Users;

use App\ActionEnum;
use App\Game;
use App\PlayerInfo;
use App\TileInfo;
use App\UserInterface;

/**
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
class Eojin implements UserInterface
{
    /** basically you hodl - no point of loitering according to law of independent trials */
    private const HODL = ActionEnum::Hold;

    public function getName(): string
    {
        return 'Eojin';
    }

    public function getMessage(): string
    {
        return '후원: 노코드 백엔드 솔루션 엔터플';
    }

    /**
     * @param PlayerInfo $player_info
     * @param TileInfo[][] $tile_info_table
     * @return ActionEnum
     */
    public function action(PlayerInfo $player_info, array $tile_info_table): ActionEnum
    {
        // let's see if it worth a dare of reaching for a shield...

        /** @var array[] $others coordinates of every other players */
        $others = [];

        /** @var array[] $shields coordinates of every shields on stage */
        $shields = [];
        foreach ($tile_info_table as $y => $tile_info_rows) {
            foreach ($tile_info_rows as $x => $tile_info) {
                if ($tile_info->exist_player) {
                    $others[] = compact('x', 'y');
                }
                if ($tile_info->exist_shield) {
                    $distanceToShield = abs($player_info->x - $x) + abs($player_info->y - $y);
                    $shields[] = compact('x', 'y', 'distance');
                }
            }
        }

        // get nearest shield
        usort($shields, function ($shield1, $shield2) {
            return $shield1['distance'] <=> $shield2['distance'];
        });
        $nearestShield = reset($shields);

        // just hodl if that shield is too far to worth a try
        if ($nearestShield['x'] + $nearestShield['y'] < Game::BOOM_TURNS) {
            return self::HODL;
        }

        // decide direction
        // todo make code cooler
        $gottaGoLeft = $nearestShield['x'] < $player_info->x;
        $gottaGoRight = $player_info->x < $nearestShield['x'];
        $gottaGoUp = $nearestShield['y'] < $player_info->y;
        $gottagoDown = $player_info->y < $nearestShield['y'];
        $nobodyLeft = empty(array_filter($others, function ($other) use ($player_info) {
            return $other['x'] + 1 === $player_info->x;
        }));
        $nobodyRight = empty(array_filter($others, function ($other) use ($player_info) {
            return $player_info->x + 1 === $other['x'];
        }));
        $nobodyAbove = empty(array_filter($others, function ($other) use ($player_info) {
            return $other['y'] + 1 === $player_info->y;
        }));
        $nobodyBelow = empty(array_filter($others, function ($other) use ($player_info) {
            return $player_info->y + 1 === $other['y'];
        }));
        if ($gottaGoLeft && $nobodyLeft) {
            return ActionEnum::Left;
        }
        if ($gottaGoUp && $nobodyAbove) {
            return ActionEnum::Up;
        }
        if ($gottagoDown && $nobodyBelow) {
            return ActionEnum::Down;
        }
        if ($gottaGoRight && $nobodyRight) {
            return ActionEnum::Right;
        }
        return self::HODL;
    }
}