<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\FloatingTextManager;

class FloatingTextCommand extends Command
{


    public function __construct()
    {
        parent::__construct("ft");
        $this->description = "Floating text management";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $help = implode("\n", [
            "Floating Text Help",
            "/ft add (name) (text)",
            "/ft list (radius)",
            "/ft edit (name) [line = 1] (text)",
            "/ft remove (name)"
        ]);
        if ($sender instanceof HorizonPlayer) {
            $level = $sender->getLevel();
            $pos = new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ());
            if ((count($args) >= 2) or (isset($args[0]) && $args[0] === "list")) {
                $arg0 = $args[0];
                array_shift($args);
                switch ($arg0) {
                    case "add":
                        if (isset($args[1])) {
                            $name = $args[0];
                            array_shift($args);
                            $text = implode("\n", $args);
                            FloatingTextManager::add($level, $pos, $name, $text);
                            $sender->sendMessage("§aSuccessfully added floating text with tag '$name' to your current position");
                        } else {
                            $sender->sendMessage("§cYou need to provide text for the floating text");
                        }
                        break;
                    case "list":
                        $list = [];
                        $radius = $args[0] ?? 10;
                        $texts = FloatingTextManager::$texts;
                        if (isset($texts[$level->getName()])) {
                            foreach ($texts[$level->getName()] as $entity) {
                                if ($sender->distance($entity) <= $radius) {
                                    $list[] = $entity->namedtag->getString("floatingtext") . " §7(§a" . round($sender->distance($entity), 2) . "m§7)";
                                }
                            }
                            $sender->sendMessage("§aFloating Texts within a §f$radius §ablock radius of you:\n§6- §b" . implode("\n§6- §b", $list));
                        } else $sender->sendMessage("§cThere are not any floating texts in your current level");
                        break;
                    case "tp":
                        $entity = FloatingTextManager::getTag($args[0], $level);
                        if ($entity !== null) {
                            $sender->teleport($entity);
                            $sender->sendMessage("§aTeleported you to entity $args[0]");
                        } else $sender->sendMessage("§cEntity with tag §f$args[0]§c not found in current world");
                        break;
                    case "reset":
                        $entity = FloatingTextManager::getTag($args[0], $level);
                        if ($entity !== null) {
                            FloatingTextManager::update($entity, $args[0], "Reset Floating Text (TAG: $args[0])");
                            $sender->sendMessage("§aSuccessfully updated floating text with tag $args[0]");
                        } else $sender->sendMessage("§cFloating text with tag $args[0] not found");
                        break;
                    case "edit":
                        if (isset($args[1])) {
                            $entity = FloatingTextManager::getTag($args[0], $level);
                            if ($entity !== null) {
                                $name = $args[0];
                                if (is_numeric($args[1])) {
                                    $text = implode(" ", array_slice($args, 2));
                                    $current = explode("\n", $entity->getNameTag());
                                    $current[(int)$args[1] - 1] = $text;
                                    $string = "";
                                    print_r($current);
                                    for ($i = 0; $i <= array_key_last($current); $i++) {
                                        $string .= isset($current[$i]) ? $current[$i] . "\n" : "\n";
                                    }
                                    var_dump($string);
                                    FloatingTextManager::update($entity, $name, $string);
                                } else {
                                    array_shift($args);
                                    $text = implode(" ", $args);
                                    FloatingTextManager::update($entity, $name, $text);
                                }
                                $sender->sendMessage("§aSuccessfully updated floating text with tag $name");
                            } else $sender->sendMessage("§cFloating text with tag $args[0] not found");
                        } else $sender->sendMessage("§cSpecify text to update this particle with");
                        break;
                    case "remove":
                        $sender->sendMessage(FloatingTextManager::remove($args[0], $level) ? "§aSuccessfully removed floating text with tag $args[0]" : "§cFalied to remove floating text with tag §f$args[0]§c, does it exist?");
                        break;
                }
            } else $sender->sendMessage($help);
        } else $sender->sendMessage("§cYou can only run this command in-game");
    }

}