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
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\CPS;
use Zedstar16\HorizonCore\components\LobbyItems\LobbyItems;
use Zedstar16\HorizonCore\components\shop\Shop;
use Zedstar16\HorizonCore\events\PlayerClickEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\hud\CreateScoreboard;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\tasks\UpdateScoreboardTask;
use Zedstar16\HorizonCore\utils\Utils;

class PlayerEventListener implements Listener
{

    public function onCreation(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(HorizonPlayer::class);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $p = $event->getPlayer();
        if(!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("blocks_broken", 1);
            }
        }
    }

    public function onChat(PlayerChatEvent $event)
    {
        $p = $event->getPlayer();
        if(!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("chat_messages", 1);
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $p = $event->getPlayer();
        if(!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("blocks_placed", 1);
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $cause = $event->getPlayer()->getLastDamageCause();
        $player = $event->getPlayer();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof HorizonPlayer) {
              $killer->getSession()->incrementValue("kills", 1);
            }
        }
        $player->getSession()->incrementValue("deaths", 1);
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $p = $event->getPlayer();
        $item = $event->getItem();
        $tags = ["ffa", "duels", "kitpvp", "profile", "crates", "stats", "info", "settings", "toys"];
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
        /*
              $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
              $menu->readonly(true);
              $menu->setName("'s Match Inventory");
              $contents = [];
              for($i = 0; $i <= 53; $i++){
                  $contents[] = Item::get(Item::DIRT)->setCustomName($i);
              }
              $menu->getInventory()->setContents($contents);
              $menu->send($p);
           */

        if ($p instanceof HorizonPlayer) {

            $string = Utils::centralise("This is an extremely long title which i am wrijk;jk;jk;\nReason: Hacking and being a mega idiot\nModerator: YourMother");
            $p->kick($string, false);
             new Shop($p);
            SessionManager::add($p);
            if ($p->isInPracticeZone()) {
                $p->setInSpawn(true);
            }
            PlayerDataManager::incrementValue($p, "joins", 1);
            $p->updateScoreTag();
        }
    }

    public function calculation(){
        $string = "You have been banned\nReason: Hacking and being a mega idiot\nModerator: YourMother\nBan does not expire";
        $lines = explode("\n", $string);
        $longest = 0;
        foreach($lines as $line){
            $len = strlen($line);
            if($len > $longest){
                $longest = $len;
            }
        }
        $midpoint = (int)$longest/2;
        $new = "";
        foreach ($lines as $line) {
            $len = strlen($line);
            $mid = (int)$len/2;
            $dist = $midpoint-$mid;
            $new .= str_repeat(" ", $dist).$line."\n";
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            $p->updateScoreTag();
        }
    }

    public function onClick(PlayerClickEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            $p->getSession()->incrementValue("clicks", 1);
            $p->updateNameTag();
            $p->sendPopup("§9CPS: §b" . CPS::calculateCPS($p));
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $to = Utils::isInsidePracticeZone($event->getTo());
        $from = Utils::isInsidePracticeZone($event->getFrom());
        $p = $event->getPlayer();
        $distance = $event->getFrom()->distance($event->getTo());
        if ($p instanceof HorizonPlayer) {
            if(!$event->isCancelled()) {
                $p->getSession()->incrementValue("distance_travelled", $distance);
            }
            if ($to && !$from) {
                $p->in_kitpvp = true;
                $p->setInSpawn(true, true);
                $p->in_kitpvp = false;
                $p->sendPopup("§aYou have entered the Practice Zone");
            } elseif ($from && !$to) {
                $p->in_kitpvp = true;
                $p->setInSpawn(false, true);
                $p->sendPopup("§6You have equipped your KitPvP Inventory");
            }
        }
    }


    public function onQuit(PlayerQuitEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            SessionManager::remove($p);
        }
    }


}