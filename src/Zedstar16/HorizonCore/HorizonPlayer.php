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
use Zedstar16\HorizonCore\components\CPS;
use Zedstar16\HorizonCore\components\Economy;
use Zedstar16\HorizonCore\managers\FFAManager;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\session\Session;
use Zedstar16\HorizonCore\utils\Utils;

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
        $ffa_players = count(FFAManager::getFFAArenaPlayers());
        $duel_players = 0;
        $map = [
            0 => ["§r§l§9> §bFFA §r§8[§6$ffa_players §7players§8] §l§9<", "ffa", 0],
            1 => ["§r§l§9> §bDuels §r§8[§6$duel_players §7players§8] §l§9<", "duels", 1],
            2 => ["§r§l§9> §bProfile §9<", "profile", 3],
            3 => ["§r§l§9> §bCrates §9<", "crates", 4],
            4 => ["§r§l§9> §bStats §9<", "stats", 5],
            5 => ["§r§l§9> §bToys & Cosmetics §9<", "toys", 6],
            6 => ["§r§l§9> §bInfo §9<", "info", 7],
            7 => ["§r§l§9> §bSettings §9<", "settings", 8],
        ];
        $a = ItemFactory::get(ItemIds::IRON_AXE);
        $b = ItemFactory::get(ItemIds::DIAMOND_SWORD);
        $c = ItemFactory::get(ItemIds::EMERALD);
        $d = ItemFactory::get(ItemIds::ENDER_CHEST);
        $e = ItemFactory::get(ItemIds::BLAZE_POWDER);
        $f = ItemFactory::get(ItemIds::FIREWORKS);
        $g = ItemFactory::get(ItemIds::BOOK);
        $h = ItemFactory::get(ItemIds::NETHER_STAR);
        $items = [$a, $b, $c, $d, $e, $f, $g, $h];
        foreach ($items as $key => $item) {
            $item->setCustomName($map[$key][0]);
            $nbt = $item->getNamedTag();
            $nbt->setString($map[$key][1], $map[$key][1]);
            $item->setCompoundTag($nbt);
            $item->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
            $this->getInventory()->setItem($map[$key][2], $item);
        }
    }

    public function setInSpawn(bool $bool, bool $savedItems = false)
    {
        if ($bool) {
            if ($savedItems) {
                $this->saveInventory();
            }
            $this->getInventory()->clearAll();
            $this->getArmorInventory()->clearAll();
            $this->setSpawnItems();
            $this->in_kitpvp = false;
        } else {
            $this->getInventory()->clearAll();
            $this->getArmorInventory()->clearAll();
            if ($savedItems) {
                $this->loadSavedInventory();
            }
            $this->in_kitpvp = true;
        }
    }

    public function isInPracticeZone()
    {
        return Utils::isInsidePracticeZone($this->getPosition());
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

    public function updateScoreTag()
    {
        $health = (int)$this->getHealth();
        $color = "§a";
        if ($health < 16 && $health >= 12) {
            $color = "§e";
        } elseif ($health < 12 && $health >= 8) {
            $color = "§6";
        } elseif ($health < 8 && $health >= 4) {
            $color = "§c";
        } elseif ($health < 4 && $health >= 0) {
            $color = "§4";
        }
        $this->setScoreTag("{$color}{$health}❤ §7| §f".$this->getSession()->getPlayerData()["Controls"]);
    }

    public function updateNameTag(){
        $string = "§9CPS: §b" . CPS::calculateCPS($this);
        $this->setNameTag($string . "\n" . $this->getName() . " ".$this->getCustomTag());
    }

    public function getCustomTag()
    {
        return "";
    }

    public function getNameTag(): string
    {
        return parent::getNameTag(); // TODO: Change the autogenerated stub
    }

    public function getEconomy(): Economy{
        return new Economy($this);
    }

}