<?php


namespace Zedstar16\HorizonCore\components\KOTH;


use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\WorldMap;
use Zedstar16\HorizonCore\Horizon;

class KothUtils
{

    public static function setFloor(bool $active)
    {
        $pos = Horizon::Config()["kothcenter"];
        $center = new Position($pos["x"], $pos["y"], $pos["z"], Server::getInstance()->getLevelByName(WorldMap::KIT));
        $radius = 3;
        for ($x = $center->x - $radius; $x <= $center->x + $radius; $x++) {
            for ($z = $center->z - $radius; $z <= $center->z + $radius; $z++) {
                $center->getLevel()->setBlock(new Vector3($x, $center->y, $z), Block::get(Block::STAINED_GLASS, $active ? 5 : 14));
            }
        }
        for ($x = $center->x - $radius; $x <= $center->x + $radius; $x++) {
            for ($z = $center->z - $radius; $z <= $center->z + $radius; $z++) {
                $center->getLevel()->setBlock(new Vector3($x, $center->y - 1, $z), Block::get(Block::STAINED_GLASS, $active ? 5 : 14));
            }
        }
    }
}