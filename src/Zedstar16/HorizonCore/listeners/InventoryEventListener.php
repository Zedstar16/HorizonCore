<?php


namespace Zedstar16\HorizonCore\listeners;


use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\inventory\PlayerInventory;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\inventory\InvMenuInventory;

class InventoryEventListener implements Listener
{

    public function onremove(InventoryTransactionEvent $event)
    {/*
        try {
            $inv = $event->getTransaction()->getInventories();
            $bool = false;
            $invmenu = null;
            $player_inventory = null;
            foreach ($inv as $inventory) {
                if ($inventory instanceof InvMenuInventory) {
                    $invmenu = $inventory;
                    $bool = true;
                }
                if ($inventory instanceof PlayerInventory) {
                    $player_inventory = $inventory;
                    if ($bool) {
                        $event->setCancelled();
                    }
                }
            }
            if ($bool) {
                $actions = $event->getTransaction()->getActions();
                foreach ($actions as $action) {
                    if (in_array($action->getSourceItem(), $invmenu->getContents()) && in_array($action->getTargetItem(), $player_inventory->getContents())) {
                        $event->setCancelled();
                    }
                }
            }
        }catch (\Throwable $error){}
*/
    }

    public function onDrop(PlayerDropItemEvent $event)
    {
        $item = $event->getItem();
        if ($item->getNamedTag()->hasTag("chest")) {
            $event->setCancelled();
        }
        $p = $event->getPlayer();
        if(!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("items_dropped");
            }
        }
    }

    public function onPickUp(InventoryPickupItemEvent $event){
       $viewers = $event->getInventory()->getViewers();
       foreach ($viewers as $player){
           if($player instanceof HorizonPlayer && !$event->isCancelled()){
               $player->getSession()->incrementValue("items_picked_up");
           }
       }
    }

    public function onConsume(PlayerItemConsumeEvent $event){
        $p = $event->getPlayer();
        if(!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("items_consumed");
            }
        }
    }
}