<?php


namespace Zedstar16\HorizonCore\listeners;


use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\CPS;
use Zedstar16\HorizonCore\components\Crate\CrateItemShiftTask;
use Zedstar16\HorizonCore\components\HUD\KitPvP;
use Zedstar16\HorizonCore\components\KOTH\KothGameManager;
use Zedstar16\HorizonCore\components\LobbyItems\LobbyItems;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\components\Moderation\TimeFormat;
use Zedstar16\HorizonCore\components\TempFloatingItem;
use Zedstar16\HorizonCore\components\WorldMap;
use Zedstar16\HorizonCore\events\AddCoinsEvent;
use Zedstar16\HorizonCore\events\AddKillStreakEvent;
use Zedstar16\HorizonCore\events\AddXPEvent;
use Zedstar16\HorizonCore\events\PlayerClickEvent;
use Zedstar16\HorizonCore\events\PlayerEnterKothEvent;
use Zedstar16\HorizonCore\events\PlayerLeaveKothEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\hud\CreateScoreboard;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\SharedInvMenu;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\FloatingTextManager;
use Zedstar16\HorizonCore\managers\KitManager;
use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\utils\Utils;

class PlayerEventListener implements Listener
{
    /** @var Server */
    public $s;

    public $interact_cooldown = [];

    public function __construct(Server $server)
    {
        $this->s = $server;
    }

    public function onCreation(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(HorizonPlayer::class);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $p = $event->getPlayer();
        if (!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("blocks_broken", 1);
            }
        }
    }

    public function onChat(PlayerChatEvent $event)
    {

        $p = $event->getPlayer();
        if (!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("chat_messages", 1);
            }
        }
        $name = $p->getLowerCaseName();
        if (ModerationAPI::isMuted($name)) {
            try {
                $data = ModerationAPI::getMute($name);
                $temp = $data["expires"] !== null;
                if ($temp) {
                    $p->sendMessage("§cYou are currently temp muted\n§5Reason: §d$data[reason]\n§6Expires In: " . TimeFormat::timeToExpiry($data["expires"]) . "\n§2Appeal at: §aappeal.horizonpe.net");
                } else $p->sendMessage("§cYou are permanently muted\n§5Reason: §d$data[reason]\n§2Appeal at: §aappeal.horizonpe.net");
            } catch (ModerationException $e) {
            }
            $event->setCancelled();
        }
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        echo 1;
        Horizon::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($event): void {
            //   if(!$event->isCancelled()){
            SessionManager::add($event->getPlayer());
            //   }
            echo 2;
            $p = $event->getPlayer();
            $name = $p->getLowerCaseName();
            if (ModerationAPI::isBanned($name)) {
                try {
                    $data = ModerationAPI::getBan($name);
                    $temp = $data["expires"] !== null;
                    if ($temp) {
                        $p->kick(Utils::centralise("§cYou are currently temp banned\n§5Reason: §d$data[reason]\n§6Ban Expires In: " . TimeFormat::timeToExpiry($data["expires"]) . "\n\n§2Appeal at: §aappeal.horizonpe.net"), false);
                    } else $p->kick(Utils::centralise("§cYou are currently permanently banned\n§5Reason: §d$data[reason]\n\n§2Appeal at: §aappeal.horizonpe.net"), false);
                } catch (ModerationException $e) {
                }
                $event->setCancelled();
            } else {
                echo 3;
                if ($p instanceof HorizonPlayer) {
                    echo 4;
                    // SessionManager::add($p);
                    //var_dump(SessionManager::$sessions);
                    var_dump("bruh");
                } else echo "lol";
            }
        }), 5);
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $p = $event->getPlayer();
        if (!$event->isCancelled()) {
            if ($p instanceof HorizonPlayer) {
                $p->getSession()->incrementValue("blocks_placed", 1);
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $cause = $event->getPlayer()->getLastDamageCause();
        $player = $event->getPlayer();
        $string = null;
        $causes = [
            EntityDamageEvent::CAUSE_CONTACT => "died from contact",
            EntityDamageEvent::CAUSE_ENTITY_ATTACK => "died to an entity",
            EntityDamageEvent::CAUSE_ENTITY_EXPLOSION => "died in an entity explosion",
            EntityDamageEvent::CAUSE_SUFFOCATION => "suffocated to death",
            EntityDamageEvent::CAUSE_FALL => "fell to their death",
            EntityDamageEvent::CAUSE_DROWNING => "drowned to death",
            EntityDamageEvent::CAUSE_FIRE => "burned to death",
            EntityDamageEvent::CAUSE_FIRE_TICK => "burned to death",
            EntityDamageEvent::CAUSE_LAVA => "burned to death",
            EntityDamageEvent::CAUSE_BLOCK_EXPLOSION => "died in an explosion",
            EntityDamageEvent::CAUSE_VOID => "was lost to the void",
            EntityDamageEvent::CAUSE_CUSTOM => "died to an unknown cause",
            EntityDamageEvent::CAUSE_STARVATION => "starved to death"
        ];
        if ($player instanceof HorizonPlayer) {
            $player->has_claimed_kit_this_life = false;
            $player->getSession()->incrementValue("deaths", 1);
            if ($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                if ($killer instanceof HorizonPlayer) {
                    $messages = ["was railed by", "was demolished by", "got rekt by", "was shanked up by", "got tossed by", "took an L to", "got boomered on by", "was zombified by"];
                    shuffle($messages);
                    $string = "§c᛭ §e{$player->getName()}§6 $messages[0] §e{$killer->getName()} §6using " . $killer->getInventory()->getItemInHand()->getName();
                    $killer = $cause->getDamager();
                    $killer->getSession()->incrementValue("kills", 1);
                    $killer->getSession()->addKillToStreak();
                }
            } elseif ($cause instanceof EntityDamageByChildEntityEvent) {
                $killer = $cause->getDamager();
                if ($killer instanceof Living) {
                    $dist = $player->distance($killer);
                    if ($dist < 10) {
                        $message = "bowspammed";
                    } elseif ($dist < 20) {
                        $message = "shot";
                    } else {
                        $message = "sniped";
                    }
                    $string = "§c᛭ §e{$player->getName()}§6 was $message by §e{$killer->getName()} §7(§f{$dist}§am)";
                }
            } elseif ($cause->getCause() === EntityDamageEvent::CAUSE_VOID) {
                if (isset($player->last_damager[0]) && microtime(true) - $player->last_damager[0]["time"] < 10) {
                    $string = "§c᛭ §e{$player->getName()}§6 was thrown into the void by §e{$player->last_damager[0]["player"]}";
                    $killer = $this->s->getPlayerExact($player->last_damager[0]["player"]);
                    if ($killer !== null) {
                        $killer->getSession()->incrementValue("kills", 1);
                        $killer->getSession()->addKillToStreak();
                    }
                }
            } elseif ($cause !== null && isset($causes[$cause->getCause()])) {
                $string = "§c☠ §e{$player->getName()}§6 " . $causes[$cause->getCause()];
            }
        }
        var_dump($cause->getCause());
        $event->setDeathMessage($string ?? "unlisted");
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $p = $event->getPlayer();
        $item = $event->getItem();
        $n = $p->getName();
        $tags = ["ffa", "duels", "kitpvp", "profile", "crates", "stats", "info", "settings", "toys"];
        $bool = true;
        if (!isset($this->interact_cooldown[$n])) {
            $this->interact_cooldown[$n] = microtime(true);
        } else {
            if (microtime(true) - $this->interact_cooldown[$n] <= 0.1) {
                $bool = false;
            }
        }
        if ($p instanceof HorizonPlayer && $bool) {
            if ($p->isInPracticeZone()) {
                foreach ($tags as $tag) {
                    $nbt = $item->getNamedTag();
                    if ($nbt->hasTag($tag)) {
                        new LobbyItems($p, $tag);
                    }
                }
            }

            $block = $event->getBlock();
            if ($block->getId() === ItemIds::CHEST) {
                $data = FileManager::getJsonData("conf");
                foreach ($data["chestkit"] as $name => $kit) {
                    if ((int)$block->x === $kit["pos"]["x"] && (int)$block->y === $kit["pos"]["y"] && (int)$block->z === $kit["pos"]["z"]) {
                        $inv = InvMenu::create(InvMenu::TYPE_CHEST);
                        $contents = [];
                        foreach ($kit["contents"] as $item) {
                            $contents[] = KitManager::parseItem($item);
                        }
                        $inv->getInventory()->setContents($contents);
                        $inv->send($p);
                        $inv->setName("Contents for Kit: " . Utils::colorize($name));
                        $inv->readonly(true);
                        $event->setCancelled();
                        break;
                    }
                }
            }
        }
    }


    public function onKothEnter(PlayerEnterKothEvent $event)
    {
        $name = $event->getPlayer()->getName();
        $p = $event->getPlayer();
        $koth = $event->getKoth();
        $koth->players_in_koth[] = $name;
        $event->getPlayer()->sendTip("§aEntering KOTH");
        if ($koth->current_capper === null) {
            $koth->setCurrentCapper($p);
        }
        if (!in_array($name, $koth->players_in_koth, true)) {
            $koth->players_in_koth[] = $name;
        }
    }

    public function onKothLeave(PlayerLeaveKothEvent $event)
    {
        $p = $event->getPlayer();
        $name = $event->getPlayer()->getName();
        $koth = $event->getKoth();
        $event->getPlayer()->sendTip("§cLeaving KOTH");
        $key = array_search($name, $koth->cap_data, true);
        if ($key !== false) {
            unset($koth->cap_data[$key]);
        }
        $key2 = array_search($name, $koth->players_in_koth, true);
        if ($key2 !== false) {
            unset($koth->players_in_koth[$key2]);
        }
        if ($name === $koth->current_capper) {
            $koth->handleCapperLeave();
        }
    }

    public function onXPGain(AddXPEvent $event)
    {
        $p = $event->getPlayer();
        $xp = $event->getExperience();
        $p->setXpLevel($xp->getLevel());
        $p->setXpProgress($xp->calculatePercentageProgression());
        //   $p->getSession()->getScoreboard()->updateLine();
    }


    public function onJoin(PlayerJoinEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            $p->getSession()->initialiseProperties();
            $to = $event->getPlayer()->getLevel();
            if (!FloatingTextManager::isLoadedIn($to)) {
                FloatingTextManager::loadIn($to);
            }
            $event->setJoinMessage("");
            if (!$event->getPlayer()->hasPlayedBefore()) {
                Server::getInstance()->broadcastMessage("§c⮚ §e{$p->getName()} §6joined Horizon for the first time!");
            }
            $string = Utils::centralise("This is an extremely long title which i am wrijk;jk;jk;\nReason: Hacking and being a mega idiot\nModerator: YourMother");
            // $p->kick($string, false);
            //  new Shop($p);
            $p->teleport(Server::getInstance()->getLevelByName(WorldMap::KIT)->getSpawnLocation());
            $p->setInSpawn(true);

            PlayerDataManager::incrementValue($p, "joins", 1);
            $p->updateScoreTag();
            $p->getSession()->getScoreboard()->setScoreboard(new KitPvP($p));
            //  $p->getSession()->getScoreboard()->setLine(10, "Hello my jiggger");
            PlayerDataManager::login($p);
            new TempFloatingItem($p, Item::get(45)->setCustomName("YEES"));
            //$inv = InvMenu::create(InvMenu::TYPE_CHEST);
            //$inv->send($p);
            // $inv->setName("Crate");
            // $inv->readonly(true);
            // $this->crate($p, $inv);
            echo "bruh";
        }
    }

    public function crate(HorizonPlayer $p, SharedInvMenu $inv)
    {
        $items = KitManager::parseContents(FileManager::getYamlData("crate")["items"]);
        Horizon::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateItemShiftTask($inv, $items, function (Item $item) use ($p) {
            $p->getInventory()->addItem($item);
            $p->sendMessage("§aYou just won§r " . $item->getCustomName());
        }), 1);
    }


    public function onKillStreakAdd(AddKillStreakEvent $event)
    {
        if ($event->player instanceof HorizonPlayer) {
            $rewards = [
                5 => [
                    "coins" => 25,
                    "xp" => 100,
                ],
                10 => [
                    "coins" => 25,
                    "xp" => 150,
                ],
                15 => [
                    "coins" => 25,
                    "xp" => 200,
                ],
                20 => [
                    "coins" => 25,
                    "xp" => 250,
                ],
                25 => [
                    "coins" => 25,
                    "xp" => 300,
                ]
            ];
            $killstreak = $event->getKillStreak();
            if (is_int($killstreak / 5) && $killstreak !== 0) {
                Server::getInstance()->broadcastMessage("{$event->player->getName()} is on a {$killstreak}x KillStreak");
                $reward = $rewards[$killstreak] ?? $rewards[25];
                $coins = $reward["coins"];
                $xp = $reward["xp"];
                $p = $event->getPlayer();
                $p->getEconomy()->addCoins($coins);
                $p->getExperience()->addExperience($xp);

            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            $p->teleport(Server::getInstance()->getLevelByName(WorldMap::KIT)->getSpawnLocation());
            $p->updateScoreTag();
            $p->setInSpawn(true, false);
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
        $to_bool = Utils::isInsidePracticeZone($event->getTo());
        $from_bool = Utils::isInsidePracticeZone($event->getFrom());
        $to = $event->getTo();
        $from = $event->getFrom();
        $p = $event->getPlayer();
        $distance = $event->getFrom()->distance($event->getTo());
        /*  var_dump($to);
          $max = Horizon::Config()["praczone"]["max"];
          $min = Horizon::Config()["praczone"]["min"];
          $a = new AxisAlignedBB($min["x"], $min["y"], $min["z"], $max["x"], $max["y"], $max["z"]);
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->minY, $a->minZ)));
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->maxY, $a->minZ)));
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->maxY, $a->maxZ)));
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->minY, $a->minZ)));
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->maxY, $a->minZ)));
          $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->maxY, $a->maxZ)));
  */
        if ($p instanceof HorizonPlayer) {
            if (!$event->isCancelled()) {
                $p->getSession()->incrementValue("distance_travelled", $distance);
            }
            if (!$p->duel_waiting) {
                if ($to_bool && !$from_bool) {
                    $p->in_kitpvp = true;
                    $p->setInSpawn(true, true);
                    $p->in_kitpvp = false;
                    $p->sendPopup("§aYou have entered the Practice Zone");
                } elseif ($from_bool && !$to_bool) {
                    $p->in_kitpvp = true;
                    $p->setInSpawn(false, true);
                    $p->sendPopup("§6You have equipped your KitPvP Inventory");
                }
            }
            $koth = KothGameManager::getCurrentGame();
            if ($koth !== null) {
                $a = $koth->aabb;
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->minY, $a->minZ)));
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->maxY, $a->minZ)));
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->minX, $a->maxY, $a->maxZ)));
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->minY, $a->minZ)));
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->maxY, $a->minZ)));
                $p->getLevel()->addParticle(new FlameParticle(new Vector3($a->maxX, $a->maxY, $a->maxZ)));
                if ($koth->active) {
                    if ($koth->isInsideArena($to) && !$koth->isInsideArena($from)) {
                        new PlayerEnterKothEvent($p, $koth);
                    } elseif (!$koth->isInsideArena($to) && $koth->isInsideArena($from)) {
                        new PlayerLeaveKothEvent($p, $koth);
                    }
                }
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $event->getPlayer()->setFood(20.0);
    }


    public function onQuit(PlayerQuitEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            SessionManager::remove($p);
        }
    }

    public function onCommandRun(CommandEvent $event)
    {
        $sender = $event->getSender();
        $args = explode(" ", $event->getCommand());
        $command = $args[0];
        array_shift($args);
        $disallowed = ["hub", "spawn", "kit"];
        if (($sender instanceof HorizonPlayer) && $sender->isInCombat() && in_array($command, $disallowed, true)) {
            $sender->sendMessage("§cYou cannot run this command in combat");
            $event->setCancelled();
        }
    }

    public function onCoinAdd(AddCoinsEvent $event)
    {
        $event->getPlayer()->getSession()->getScoreboard()->updateLine("Coins", $event->getNewBalance());
    }


}