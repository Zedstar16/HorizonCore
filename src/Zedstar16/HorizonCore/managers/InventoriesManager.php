<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class InventoriesManager
{

    public static function parseInventory(array $data)
    {
        $contents = [];
        foreach ($data as $slot => $item) {
            $contents[$slot] = Item::jsonDeserialize($item);
        }
        return $contents;
    }




}