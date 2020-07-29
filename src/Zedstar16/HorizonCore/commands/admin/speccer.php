<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Server;
use specter\network\SpecterPlayer;
use specter\Specter;

class speccer extends Command
{


    public function __construct()
    {
        parent::__construct("sp");
        $this->description = "cmd desc";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $s = Server::getInstance();
        if (isset($args[0])) {
            switch ($args[0]) {
                case "s":
                    $count = $args[1] ?? 1;
                    /** @var Specter $pl */
                    $pl = $s->getPluginManager()->getPlugin("Specter");
                    for ($i = 1; $i <= $count; $i++) {
                        $pl->getInterface()->openSession("{$i}tester", "SPECTER", 19133);
                    }
                    break;
                case "t":
                    foreach ($s->getOnlinePlayers() as $p) {
                        if ($p instanceof SpecterPlayer) {
                            $p->teleport($sender);
                        }
                    }
                    break;
                case "tt":
                    $p = $sender;
                    $p->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
                    $p->level->broadcastLevelEvent($p->add(0, $p->eyeHeight, 0), LevelEventPacket::EVENT_SOUND_TOTEM);
            }
        }
    }
}