<?php


namespace Zedstar16\HorizonCore\listeners;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\LobbyItems\LobbyItems;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\hud\CreateScoreboard;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\tasks\UpdateScoreboardTask;

class PlayerEventListener implements Listener
{

    public function onCreation(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(HorizonPlayer::class);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $pn = $event->getPlayer()->getLowerCaseName();
        $blocks = isset(Horizon::$players[$pn]["blocksbroken"]) ? Horizon::$players[$pn]["blocksbroken"]++ : 1;
        Horizon::$players[$pn]["blocksbroken"] = $blocks;
    }

    public function onChat(PlayerChatEvent $event)
    {
        $pn = $event->getPlayer()->getLowerCaseName();
        $messages = isset(Horizon::$players[$pn]["chatmessages"]) ? Horizon::$players[$pn]["chatmessages"]++ : 1;
        Horizon::$players[$pn]["chatmessages"] = $messages;
        $p = $event->getPlayer();
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $pn = $event->getPlayer()->getLowerCaseName();
        $blocks = isset(Horizon::$players[$pn]["blocksplaced"]) ? Horizon::$players[$pn]["blocksplaced"]++ : 1;
        Horizon::$players[$pn]["blocksplaced"] = $blocks;
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $cause = $event->getPlayer()->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof HorizonPlayer) {
                $player = $event->getPlayer();
            }
        }
    }

    public function pakitYEEEEEEEET(DataPacketReceiveEvent $event)
    {
        if ($event->getPacket() instanceof LoginPacket) {
            $packet = (array)$event->getPacket();
            $clientdata = $packet["clientData"];
            $player = $event->getPlayer();
            $name = $clientdata["ThirdPartyName"] ?? $event->getPlayer()->getName();
            $ip = $player->getAddress();
            $deviceID = $clientdata["DeviceId"];
            Horizon::$players[$name]["clientData"] = $clientdata;
        }
    }


    public function onInteract(PlayerInteractEvent $event)
    {
        $p = $event->getPlayer();
        $item = $event->getItem();
        $tags = ["ffa", "duels", "kitpvp", "profile", "crates", "stats", "info", "settings"];
        if ($p instanceof HorizonPlayer) {
            if ($p->isInPracticeZone()) {
                foreach ($tags as $tag) {
                    $nbt = $item->getNamedTag();
                    if ($nbt->hasTag($tag)) {
                        new LobbyItems($p, $tag);
                    }
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $p = $event->getPlayer();
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->readonly(true);
        $menu->setName("'s Match Inventory");
        $contents = [];
        for($i = 0; $i <= 53; $i++){
            $contents[] = Item::get(Item::DIRT)->setCustomName($i);
        }
        $menu->getInventory()->setContents($contents);
        $menu->send($p);

        if ($p instanceof HorizonPlayer) {
            SessionManager::add($p);
        }

        // CreateScoreboard::add($p);
        // HorizonCrate::getInstance()->getScheduler()->scheduleRepeatingTask(new UpdateScoreboardTask($p), HorizonCrate::getInstance()->Config()["scoreboardtick"]);
        // $this->initialiseData($event->getPlayer()->getLowerCaseName());
    }


    public function onQuit(PlayerQuitEvent $event)
    {
        $p = $event->getPlayer();
        $name = $p->getName();
        if ($p instanceof HorizonPlayer) {
            SessionManager::remove($p);
        }
    }


}