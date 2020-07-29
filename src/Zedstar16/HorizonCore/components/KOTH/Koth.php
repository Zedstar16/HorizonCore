<?php


namespace Zedstar16\HorizonCore\components\KOTH;


use pocketmine\level\Level;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\WorldMap;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;

class Koth
{
    /** @var Server */
    public $s;
    /** @var Int */
    public $baseTick;
    /** @var AxisAlignedBB */
    public $aabb;
    /** @var Level */
    public $level;
    /** @var HorizonPlayer */
    public $current_capper;
    /** @var Int */
    public $current_cap_time;
    /** @var array */
    public $cap_data = [];
    /** @var array */
    public $players_in_koth = [];
    /** @var bool */
    public $active = false;
    /** @var bool */
    public $ended = false;


    public function __construct(Server $server)
    {
        $this->s = $server;
        $this->baseTick = 0;
        $pos = Horizon::Config()["kothcenter"];
        $c = new Position($pos["x"], $pos["y"], $pos["z"], Server::getInstance()->getLevelByName(WorldMap::KIT));
        $this->aabb = new AxisAlignedBB($c->x - 4, $c->y, $c->z - 4, $c->x + 4, $c->y + 4, $c->z + 4);
        $this->level = $this->s->getLevelByName(WorldMap::KIT);
    }

    public function start()
    {
        $this->s->broadcastMessage("[Horizon] A new KOTH event is now starting");
        KothUtils::setFloor(true);
        $this->active = true;
    }

    public function tick()
    {
        $this->baseTick++;
        if ($this->baseTick === 0) {
            $this->s->broadcastMessage("[Horizon] New KOTH event is starting in 1 minute");
        } elseif ($this->baseTick === 30) {
            $this->s->broadcastMessage("[Horizon] New KOTH event is starting in 30 seconds");
        } elseif ($this->baseTick === 45) {
            $this->s->broadcastMessage("[Horizon] New KOTH event is starting in 15 seconds");
        } elseif ($this->baseTick === 60) {
            $this->s->broadcastMessage("[Horizon] New KOTH event has started!");
        } elseif ($this->baseTick === 900) {
            if ($this->current_capper === null) {
                $this->s->broadcastMessage("[Horizon] KOTH event finished with no winners");
                $this->finish();
            } else $this->finish($this->current_capper);
        }
        if ($this->current_capper !== null) {
            $this->current_cap_time++;
            if ($this->current_cap_time >= 45) {
                $this->finish($this->current_capper);
            } else $this->current_capper->sendTip("Capturing: " . abs($this->current_cap_time - 45));
        }
    }

    public function finish(HorizonPlayer $capper = null)
    {
        KothUtils::setFloor(false);
        $this->active = false;
        if ($capper !== null) {
            $capper->getExperience()->addExperience(2500);
            $items = [
            ];
            foreach ($items as $item) {
                if ($capper->getInventory()->canAddItem($item)) {
                    $capper->getInventory()->addItem($item);
                }
            }
            $capper->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
            $this->level->addParticle(new ExplodeParticle($capper));
            $this->s->broadcastMessage("[Horizon] {$capper->getName()} has captured KOTH!");
        }
        $this->ended = true;
    }

    public function handleCapperLeave()
    {
        if ($this->current_capper !== null) {
            $this->current_capper->setScoreTag($this->cap_data["capper-scoretag"]);
        }
        $this->current_capper = null;
        $this->current_cap_time = 0;
        foreach ($this->players_in_koth as $i => $iValue) {
            if (isset($this->players_in_koth[$i])) {
                $p = Server::getInstance()->getPlayer($iValue);
                if ($p !== null) {
                    $this->current_capper = $p->getName();
                    $this->setCurrentCapper($p);
                    break;
                }
            }
        }
    }

    public function setCurrentCapper(Player $p)
    {
        $this->current_capper = $p;
        $this->cap_data["capper-scoretag"] = $p->getScoreTag();
        $p->setScoreTag("§a§lCurrent Capper\n" . $p->getScoreTag());
    }

    public function isInsideArena(Position $pos)
    {
        return $this->level === $pos->level && $this->aabb->isVectorInside($pos);
    }

    public function getKoth(): Koth
    {
        return $this;
    }


}