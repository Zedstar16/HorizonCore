<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Constants;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\ZDuels\Main;

class KitManager
{

    public static function parseItem(string $string): Item
    {
        $data = explode(":", $string);
        $id = (int)$data[0];
        $damage = (int)$data[1];
        $count = (int)$data[2];
        $item = ItemFactory::get($id, $damage, $count);
        if (isset($data[3])) {
            if ($data[3] !== "DEFAULT") {
                $item->setCustomName(str_replace("&", "§", $data[3]));
            }
            if (isset($data[4])) {
                $data = array_slice($data, 4);
                foreach ($data as $i => $iValue) {
                    if (is_int($i / 2)) {
                        $enchant = Enchantment::getEnchantmentByName($iValue);
                        $instance = new EnchantmentInstance($enchant, $data[$i + 1]);
                        $item->addEnchantment($instance);
                        $i++;
                    } else $i++;
                }
                return $item;
            } else return $item;
        } else return $item;
    }

    public static function indexItem(Item $item): string
    {
        $store = [];
        $store[] = $item->getId();
        $store[] = $item->getDamage();
        $store[] = $item->getCount();
        if ($item->hasCustomName() or $item->hasEnchantments()) {
            if ($item->hasCustomName()) {
                $store[3] = str_replace("§", "&", $item->getCustomName());
            } else $store[3] = "DEFAULT";
            if ($item->hasEnchantments()) {
                foreach ($item->getEnchantments() as $enchantment) {
                    $store[] = Constants::$enchantment_by_id[$enchantment->getId()];
                    $store[] = $enchantment->getLevel();
                }
            }
        }
        return implode(":", $store);
    }

    public static function indexContents(array $contents)
    {
        $data = [];
        foreach ($contents as $index => $item) {
            $data[$index] = self::indexItem($item);
        }
        return $data;
    }

    public static function parseContents(array $contents)
    {
        $data = [];
        foreach ($contents as $index => $itemstring) {
            $data[$index] = self::parseItem($itemstring);
        }
        return $data;
    }


    public static function getKit(Player $player, $kit, int $type, $default = false): ?array
    {
        $contents = [];
        $type = Constants::$kit[$type];
        $playerdata = PlayerDataManager::getData($player->getName())["customkits"][$type][$kit] ?? null;
        $normal = FileManager::getYamlData("$type/$kit");
        $data = $playerdata !== null ? (!$default ? $playerdata : $normal) : $normal;
        foreach ($data["armor"] as $slot => $armor) {
            $contents["armor"][$slot] = self::parseItem($armor);
        }
        foreach ($data["inventory"] as $slot => $item) {
            $contents["inventory"][$slot] = self::parseItem($item);
        }
        return $contents;
    }

    public static function saveCustomKit(Player $player, $kit, $type, $data)
    {
        $contents = [];
        $type = Constants::$kit[$type];
        $playerdata = FileManager::getJsonData("players/" . $player->getLowerCaseName());
        $playerdata["customkits"][$type][$kit] = $data;
        FileManager::saveJsonData("players/" . $player->getLowerCaseName(), $playerdata);
        return $contents;
    }

    public static function syncDuelKits()
    {
        /** @var Main $duels */
        $duels = Server::getInstance()->getPluginManager()->getPlugin("ZDuels");
        if ($duels !== null) {
            $kits = yaml_parse_file($duels->getDataFolder() . "kits.yml");
            foreach ($kits as $kitname => $data) {
                $data = [
                    "armor" => $data["armor"],
                    "inventory" => $data["item"]
                ];
                FileManager::saveYamlData("duels/$kitname", $data);
            }
        }
    }


}