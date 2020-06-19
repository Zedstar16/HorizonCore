<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\Constants;

class KitManager
{

    public static function parseItem(string $string): Item
    {
        $data = explode(":", $string);
        $id = $data[0];
        $damage = $data[1];
        $count = $data[2];
        $item = ItemFactory::get($id, $damage, $count);
        if (isset($data[3])) {
            if ($data[3] !== "DEFAULT") {
                $item->setCustomName(str_replace("&", "§", $data[3]));
            }
            if (isset($data[4])) {
                $data = array_slice($data, 4);
                print_r($data);
                for ($i = 0; $i < count($data); $i++) {
                    if (is_int($i / 2)) {
                        $enchant = Enchantment::getEnchantmentByName($data[$i]);
                        $instance = new EnchantmentInstance($enchant, $data[$i + 1]);
                        $item->addEnchantment($instance);
                        $i++;
                    } else $i++;
                }
                return $item;
            } else return $item;
        } else return $item;
    }

    public static function indexContents(array $contents)
    {
        $data = [];
        foreach ($contents as $index => $item) {
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
            $data[$index] = implode(":", $store);
        }
        return $data;
    }

    public static function getKit(Player $player, $kit, Int $type): ?array
    {
        $contents = [];
        $type = Constants::$kit[$type];
        $playerdata = PlayerDataManager::getData($player->getName())["customkits"][$type][$kit];
        $data = !empty($playerdata) ? $playerdata : FileManager::getYamlData("$type/$kit");
        foreach($data["armor"] as $slot => $armor){
            $contents["armor"][$slot] = self::parseItem($armor);
        }
        foreach($data["inventory"] as $slot => $item){
            $contents["inventory"][$slot] = self::parseItem($item);
        }
        return $contents;
    }



}