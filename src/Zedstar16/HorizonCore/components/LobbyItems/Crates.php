<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
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
        //////////////////////////////////////////
        // might not use this method            //
        // come back to                         //
        //////////////////////////////////////////
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $contents = [];
        $data = PlayerDataManager::getData($this->p)["crates"];
        $basic = ItemFactory::get(ItemIds::CHEST, 0, 1)->setCustomName("§r§l§fBASIC §r§7Crate");
        $rare = ItemFactory::get(ItemIds::ENDER_CHEST, 0, 1)->setCustomName("§r§l§bRARE §r§7Crate");
        $legendary = ItemFactory::get(ItemIds::ENDER_CHEST, 0, 1)->setCustomName("§r§l§6LEGENDARY §r§7Crate");
        $legendary->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
        $horizon = ItemFactory::get(231)->setCustomName("§r§l§cH§6O§cR§6I§cZ§6O§cN §r§7Crate");
        $horizon->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
        $koth = ItemFactory::get(234)->setCustomName("§r§l§cKOTH §r§7Crate");
        $koth->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
        $vote = ItemFactory::get(225)->setCustomName("§r§l§aVOTE §r§7Crate");
        $vote->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));

        foreach ($data as $crateType) {
            foreach ($crateType as $crate) {

            }
        }
        $menu->readonly(true);
        $menu->setInventoryCloseListener(function (Player $player, InvMenuInventory $inventory) {
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