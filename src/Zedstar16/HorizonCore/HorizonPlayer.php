<?php


namespace Zedstar16\HorizonCore;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\session\Session;

class HorizonPlayer extends Player
{

    public $data = [];

    public $lasthit = 0;

    public $in_kitpvp = false;

    const COMBAT_TIMER = 15;

    public function canEdit()
    {
        return true;
    }

    public function isInCombat(): bool
    {
        return $this->lasthit + self::COMBAT_TIMER <= time();
    }

    public function getCombatTimeRemaining(): ?int
    {
        $time = $this->lasthit + self::COMBAT_TIMER - time();
        if ($time > 0) {
            return $time;
        }
        return null;
    }

    public function isInSpawnArea()
    {

    }

    public function getSession(): ?Session
    {
        return SessionManager::getSession($this);
    }

    public function setSpawnItems()
    {
        $map = [
            0 => ["§r§l§9> §bFFA §9<", "ffa", 0],
            1 => ["§r§l§9> §bDuels §9<", "duels", 1],
            2 => ["§r§l§9> §bProfile §9<", "profile", 3],
            3 => ["§r§l§9> §bCrates §9<", "crates", 4],
            4 => ["§r§l§9> §bStats §9<", "stats", 5],
            5 => ["§r§l§9> §bInfo §9<", "info", 7],
            6 => ["§r§l§9> §bSettings §9<", "settings", 7],
        ];
        $a = ItemFactory::get(ItemIds::IRON_AXE);
        $b = ItemFactory::get(ItemIds::DIAMOND_SWORD);
        $c = ItemFactory::get(ItemIds::EMERALD);
        $d = ItemFactory::get(ItemIds::ENDER_CHEST);
        $e = ItemFactory::get(ItemIds::BLAZE_POWDER);
        $f = ItemFactory::get(ItemIds::BOOK);
        $g = ItemFactory::get(ItemIds::NETHER_STAR);
        $items = [$a, $b, $c, $d, $e, $f, $g];
        foreach ($items as $key => $item) {
            $item->setCustomName($map[$key][0]);
            $nbt = $item->getNamedTag();
            $nbt->setString($map[$key][1], $map[$key][1]);
            $item->setCompoundTag($nbt);
            $item->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
            $this->getInventory()->setItem($map[$key][2], $item);
        }
    }

    public function setInSpawn(bool $bool)
    {
        if ($bool) {
            if ($this->in_kitpvp) {
                $this->saveInventory();
            }
            $this->getInventory()->clearAll();
            $this->getArmorInventory()->clearAll();
            $this->setSpawnItems();
        } else {
            $this->getInventory()->clearAll();
            $this->getArmorInventory()->clearAll();
            if ($this->in_kitpvp) {
                $this->loadSavedInventory();
            }
        }
    }

    public function isInPracticeZone()
    {
        $aabb = new AxisAlignedBB(0, 0, 0, 100, 100, 100);
        return $aabb->isVectorInside($this->getPosition());
    }

    public function saveInventory()
    {
        $data = FileManager::getJsonData("inventory");
        foreach ($this->getInventory()->getContents() as $slot => $item) {
            $data[$this->getName()]["inventory"][$slot] = $item->jsonSerialize();
        }
        foreach ($this->getArmorInventory()->getContents() as $slot => $item) {
            $data[$this->getName()]["armorinventory"][$slot] = $item->jsonSerialize();
        }
        FileManager::saveJsonData("inventory", $data);
        $this->getInventory()->clearAll();
        $this->getArmorInventory()->clearAll();
        $this->getCursorInventory()->clearAll();
    }

    public function hasSavedInventory(): bool
    {
        return isset(FileManager::getJsonData("inventory")[$this->getName()]);
    }


    public function clearInventory()
    {
        $this->getInventory()->clearAll();
        $this->getArmorInventory()->clearAll();
        $this->getCursorInventory()->clearAll();
    }

    public function loadSavedInventory()
    {
        if ($this->hasSavedInventory()) {
            $data = FileManager::getJsonData("inventory")[$this->getName()];
            if (isset($data["inventory"])) {
                foreach ($data["inventory"] as $slot => $item) {
                    $this->getInventory()->setItem($slot, Item::jsonDeserialize($item));
                }
            }
            if (isset($data["armorinventory"])) {
                foreach ($data["armorinventory"] as $slot => $item) {
                    $this->getArmorInventory()->setItem($slot, Item::jsonDeserialize($item));
                }
            }
            unset($data[$this->getName()]);
            FileManager::saveJsonData("inventory", $data);
        }
    }

    public function teleportToSpawn()
    {
        $this->teleport($this->getServer()->getLevelByName("Voyager")->getSpawnLocation());
    }

    public function getNameTag(): string
    {
        return parent::getNameTag(); // TODO: Change the autogenerated stub
    }

}