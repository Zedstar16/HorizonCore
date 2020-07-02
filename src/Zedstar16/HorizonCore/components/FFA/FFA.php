<?php


namespace Zedstar16\HorizonCore\components\FFA;


use pocketmine\level\Level;
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
    /** @var Level */
    private $level;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->level = Server::getInstance()->getLevelByName($this->cfg()["level"]);
        if ($this->level == null) {
            Server::getInstance()->loadLevel($this->cfg()["level"]);
            $this->level = Server::getInstance()->getLevelByName($this->cfg()["level"]);
        }
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
        $player->gamemode = Constants::GAMEMODE_FFA;
        $player->clearInventory();
        $player->teleport($this->level->getSpawnLocation());
        $this->addInventoryContents($player);
        $player->getSession()->getScoreboard()->setScoreboard(new \Zedstar16\HorizonCore\components\HUD\FFA($player));
    }

    public function addInventoryContents(HorizonPlayer $player)
    {
        $kit = KitManager::getKit($player, $this->name, Constants::KIT_FFA);
        $player->getArmorInventory()->setContents($kit["armor"]);
        $player->getInventory()->setContents($kit["inventory"]);
    }

    public function getPlayers()
    {
        if ($this->level !== null) {
            return $this->level->getPlayers();
        }
        return [];
    }


}