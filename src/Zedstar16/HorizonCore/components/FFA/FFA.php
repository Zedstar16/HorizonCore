<?php


namespace Zedstar16\HorizonCore\components\FFA;


use pocketmine\level\Position;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Constants;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\KitManager;

class FFA
{

    public const ARMOR = 0;
    public const INVENTORY = 1;

    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function cfg()
    {
        return FileManager::getYamlData("ffa/$this->name");
    }

    public function teleportToArena(HorizonPlayer $player)
    {
        $player->clearInventory();
        $player->teleport(Server::getInstance()->getLevelByName($this->cfg()["level"])->getSpawnLocation());
    }

    public function addInventoryContents(HorizonPlayer $player)
    {
        $kit = KitManager::getKit($this, $this->name, Constants::KIT_FFA);
        $armor = $kit["armor"];
        $armorcontents  = [];
        foreach($armor as $slot => $item){
            $armorcontents[$slot] = KitManager::parseItem($item);
        }
        $contents  = [];
        $inventory = $kit["inventory"];
        foreach($armor as $slot => $item){
            $contents[$slot] = KitManager::parseItem($item);
        }
        $player->getArmorInventory()->setContents($armor);
        $player->getInventory()->setContents($contents);
    }


}