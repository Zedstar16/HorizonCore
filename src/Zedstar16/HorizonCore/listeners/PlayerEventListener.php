<?php


namespace Zedstar16\HorizonCore\listeners;


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
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\CPS;
use Zedstar16\HorizonCore\components\HUD\KitPvP;
use Zedstar16\HorizonCore\components\KOTH\KothGameManager;
use Zedstar16\HorizonCore\components\LobbyItems\LobbyItems;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\components\Moderation\TimeFormat;
use Zedstar16\HorizonCore\events\AddCoinsEvent;
use Zedstar16\HorizonCore\events\AddKillStreakEvent;
use Zedstar16\HorizonCore\events\AddXPEvent;
use Zedstar16\HorizonCore\events\PlayerClickEvent;
use Zedstar16\HorizonCore\events\PlayerEnterKothEvent;
use Zedstar16\HorizonCore\events\PlayerLeaveKothEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\hud\CreateScoreboard;
use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\managers\SessionManager;
use Zedstar16\HorizonCore\utils\Utils;

class PlayerEventListener implements Listener
{
    /** @var Server */
    public $s;

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
        Horizon::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($event): void {
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
            }
        }), 20);
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
        if ($player instanceof HorizonPlayer) {
            $player->has_claimed_kit_this_life = false;
            $player->getSession()->incrementValue("deaths", 1);
            if ($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                if ($killer instanceof HorizonPlayer) {
                    $messages = ["was railed by", "was demolished by", "got rekt by", "was shanked up by", "got tossed by", "took an L to", "got boomered on by", "was zombified by"];
                    shuffle($messages);
                    $event->setDeathMessage("§c᛭ §e{$player->getName()}§6 $messages[0] §e{$killer->getName()} §6using " . $killer->getInventory()->getItemInHand()->getName());
                    $killer = $cause->getDamager();
                    $killer->getSession()->incrementValue("kills", 1);
                    $killer->getSession()->addKillToStreak();
                }
            } elseif ($cause instanceof EntityDamageByChildEntityEvent) {
                $killer = $cause->getDamager();
                if ($killer instanceof HorizonPlayer) {
                    $dist = $player->distance($killer);
                    if ($dist < 10) {
                        $message = "bowspammed";
                    } elseif ($dist < 20) {
                        $message = "shot";
                    } else {
                        $message = "sniped";
                    }
                    $event->setDeathMessage("§c᛭ §e{$player->getName()}§6 was $message by §e{$killer->getName()} §7(§f{$dist}§em)");
                }
            } elseif ($cause->getCause() === EntityDamageEvent::CAUSE_VOID) {
                if (isset($player->last_damager[0]) && microtime(true) - $player->last_damager[0]["time"] < 10) {
                    $event->setDeathMessage("§c᛭ §e{$player->getName()}§6 was thrown into the void by §e{$player->last_damager[0]["player"]}");
                    $killer = $this->s->getPlayerExact($player->last_damager[0]["player"]);
                    if ($killer !== null) {
                        $killer->getSession()->incrementValue("kills", 1);
                        $killer->getSession()->addKillToStreak();
                    }
                }
            } elseif ($cause !== null && isset($causes[$cause->getCause()])) {
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
                    EntityDamageEvent::CAUSE_VOID => "was taken by the void",
                    EntityDamageEvent::CAUSE_CUSTOM => "died to an unknown cause",
                    EntityDamageEvent::CAUSE_STARVATION => "starved to death"
                ];
                $event->setDeathMessage("§c🕱 §e{$player->getName()}§6 " . $causes[$cause->getCause()]);
            } else {
                $event->setDeathMessage("");
            }
        }
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
        if ($name === $koth->current_capper) {
            $koth->handleCapperLeave();
        }
    }

    public function onXPGain(AddXPEvent $event)
    {
        $xp = $event->getExperience();
        $event->getPlayer()->setXpLevel($xp->getLevel());
        $event->getPlayer()->setXpProgress($xp->calculatePercentageProgression());
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $p = $event->getPlayer();
        if ($p instanceof HorizonPlayer) {
            $event->setJoinMessage("");
            if (!$event->getPlayer()->hasPlayedBefore()) {
                Server::getInstance()->broadcastMessage("§c⮚ §e{$p->getName()} §6joined Horizon for the first time!");
            }
            $string = Utils::centralise("This is an extremely long title which i am wrijk;jk;jk;\nReason: Hacking and being a mega idiot\nModerator: YourMother");
            // $p->kick($string, false);
            //  new Shop($p);
            SessionManager::add($p);
            $p->teleport(Server::getInstance()->getLevelByName("kit2")->getSpawnLocation());
            $p->setInSpawn(true);

            PlayerDataManager::incrementValue($p, "joins", 1);
            $p->updateScoreTag();
            $p->getSession()->getScoreboard()->setScoreboard(new KitPvP($p));
            //  $p->getSession()->getScoreboard()->setLine(10, "Hello my jiggger");
        }
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
                if ($koth->isInsideArena($to) && !$koth->isInsideArena($from)) {
                    new PlayerEnterKothEvent($p, $koth);
                } elseif (!$koth->isInsideArena($to) && $koth->isInsideArena($from)) {
                    new PlayerLeaveKothEvent($p, $koth);
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
            print_r($p->getSession()->data);
            SessionManager::remove($p);
        }
    }

    public function onCommandRun(CommandEvent $event)
    {
        $sender = $event->getSender();
        $args = explode(" ", $event->getCommand());
        $command = $args[0];
        array_shift($args);
        if ($sender instanceof HorizonPlayer) {
            if ($sender->isInCombat()) {

            }
        }
    }

    public function onCoinAdd(AddCoinsEvent $event)
    {
        $event->getPlayer()->getSession()->getScoreboard()->updateLine("Coins", $event->getNewBalance());
    }


}