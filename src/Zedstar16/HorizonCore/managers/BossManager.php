<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\entity\Entity;
use pocketmine\level\Position;
use Throwable;
use Zedstar16\HorizonCore\utils\Utils;

class BossManager
{

    public static function spawnBoss(Position $pos, string $boss): bool
    {
        if ($pos->level->isChunkGenerated($pos->x >> 4, $pos->z >> 4) === true) {
            $nbt = Entity::createBaseNBT($pos);
            $entity = Entity::createEntity($boss, $pos->level, $nbt);
            try {
                $entity->spawnToAll();
            } catch (Throwable $e) {
                Utils::error($e);
                return false;
            }
        }
        return true;
    }

}