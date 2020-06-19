<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\inventory\InvMenuInventory;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\managers\PlayerDataManager;

class Crates
{
    /** @var Player */
    public $p;

    public function __construct(Player $player)
    {
        $this->p = $player;
        $this->primary();
    }

    public function getCrates(){
        PlayerDataManager::getData($this->p->getName())["crates"];
    }

    public function primary()
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $contents = [];
        $menu->readonly(true);
        $menu->setInventoryCloseListener(function (Player $player, InvMenuInventory $inventory){
            $inv = $player->getInventory()->getContents();
            foreach ($inv as $item) {
                if ($item->getNamedTag()->hasTag("chest")) {
                    $player->getInventory()->remove($item);
                }
            }
        });
        $menu->setName("'Your Crates");
        $menu->getInventory()->setContents($contents);
        $menu->send($this->p);
    }

}