<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class WorldCommand extends Command
{

    public function __construct()
    {
        parent::__construct("world");
        $this->description = "World Management Command";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $s = Server::getInstance();
        $help = [
            "tp (world) [player] - Teleport to a world, or tp a player to a world",
            "list - List all worlds"
        ];
        if (isset($args[0])) {
            switch ($args[0]) {
                case "tp":
                    if (isset($args[1])) {
                        $level = $s->getLevelByName($args[1]);
                        if ($level !== null) {
                            $target = $sender;
                            if (isset($args[2])) {
                                $target = $s->getPlayer($args[2]);
                                if ($target == null) {
                                    $sender->sendMessage("$args[2] is not online");
                                    return false;
                                }
                            } else {
                                if (!$sender instanceof Player) {
                                    $sender->sendMessage("You need to specify a player to teleport to a world");
                                    return false;
                                }
                            }
                            $target->teleport($level->getSpawnLocation());
                        }
                    } else $sender->sendMessage("World $args[1] not found");
                    break;
                case "list":
                    $list = "";
                    foreach ($s->getLevels() as $level) {
                        $loaded = $s->isLevelLoaded($level->getName()) ? "[loaded] (" . count($level->getPlayers()) . " players)" : "[unloaded]";
                        $list .= "- {$level->getName()} $loaded\n";
                    }
                    if ($sender instanceof Player) {
                        $form = new SimpleForm(function (Player $player, $data = null) {
                            if ($data === null) {
                                return;
                            }
                        });
                        $form->setTitle("World List");
                        $form->setContent($list);
                        $form->addButton("Ok");
                        $sender->sendForm($form);
                    } else $sender->sendMessage($list);
                    break;
                default:
                    $sender->sendMessage(implode("\n/worlds ", $help));
                    break;
            }
        } else $sender->sendMessage(implode("\n/worlds ", $help));
        return true;
    }

}