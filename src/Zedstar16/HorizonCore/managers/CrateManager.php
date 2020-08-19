<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\Crate\Crate;
use Zedstar16\HorizonCore\components\Kits\Kit;

class CrateManager
{
    /** @var Crate[] */
    public static $crates = [];

    private static $positions = [];

    public static function registerCrates()
    {
        $data = FileManager::getYamlData("crates");
        foreach ($data as $cratename => $cratedata) {
            $pos = $cratedata["pos"];
            self::$positions[] = new Vector3($pos["x"], $pos["y"], $pos["z"]);
            self::$crates[] = new Crate($cratename, KitManager::parseContents($cratedata["items"]));
        }
    }

    public static function getCrate(string $name): ?Crate
    {
        foreach (self::$crates as $crate) {
            if ($crate->getName() === $name) {
                return $crate;
            }
        }
        return null;
    }

    public static function isCrate(Block $block)
    {
        return in_array($block->asVector3(), self::$positions, true);
    }

}