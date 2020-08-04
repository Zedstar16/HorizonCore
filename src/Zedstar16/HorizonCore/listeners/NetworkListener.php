<?php


namespace Zedstar16\HorizonCore\listeners;

use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use Zedstar16\HorizonCore\events\PlayerClickEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\managers\FloatingTextManager;

class NetworkListener implements Listener
{

    public function onDataPacketReceive(DataPacketReceiveEvent $event)
    {
        $p = $event->getPlayer();
        $pk = $event->getPacket();
        if ($event->getPacket() instanceof LoginPacket) {
            $packet = (array)$event->getPacket();
            $clientdata = $packet["clientData"];
            $player = $event->getPlayer();
            $name = $clientdata["ThirdPartyName"] ?? $event->getPlayer()->getName();
            $ip = $player->getAddress();
            $deviceID = $clientdata["DeviceId"] ?? "11111111111";
            Horizon::$players[$name]["clientData"] = $clientdata;
            //  Horizon::$players[$event->getPlayer()->getName()] = $pk->clientData;
        }
        if ($pk::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID && $pk->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
            new PlayerClickEvent($p);
        } elseif ($pk::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $pk->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
            new PlayerClickEvent($p);
        } elseif ($pk::NETWORK_ID === PlayerActionPacket::NETWORK_ID && $pk->action === PlayerActionPacket::ACTION_START_BREAK && $p->getGamemode() == 2) {
            new PlayerClickEvent($p);
        }
    }

    public function onLevelLoad(LevelLoadEvent $event)
    {
        FloatingTextManager::loadIn($event->getLevel());
    }

    public function onLevelUnload(LevelUnloadEvent $event)
    {
        FloatingTextManager::unload($event->getLevel());
    }


}