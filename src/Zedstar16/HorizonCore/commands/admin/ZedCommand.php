<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\particle\LavaParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use Zedstar16\HorizonCore\components\Crate\CrateItemShiftTask;
use Zedstar16\HorizonCore\components\TempFloatingItem;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\SharedInvMenu;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\KitManager;

class ZedCommand extends Command
{


    public function __construct()
    {
        parent::__construct("zed");
        $this->setAliases(["z"]);
        $this->description = "Internals usage for Zed";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0]) && $sender instanceof Player) {
            switch ($args[0]) {
                case "rn":
                case "rename":
                    if (isset($args[1])) {
                        array_shift($args);
                        $name = implode(" ", $args);
                        $item = $sender->getInventory()->getItemInHand();
                        $item->setCustomName("§r" . $name);
                        $sender->getInventory()->setItemInHand($item);
                        $sender->sendMessage("§aRenamed the item in your hand to §r$name");
                    }
                    break;
                case "testcrate":
                    $p = $sender;
                    $inv = InvMenu::create(InvMenu::TYPE_CHEST);
                    $inv->send($p);
                    $inv->setName("Crate");
                    $inv->readonly(true);
                    $this->crate($p, $inv);
                    break;
                case "item":
                    new TempFloatingItem($sender, Item::get(ItemIds::DIAMOND_SWORD)->setCustomName("YEES"), 5);
                    break;
            }
        }
    }

    public function crate(HorizonPlayer $p, SharedInvMenu $inv)
    {
        $items = KitManager::parseContents(FileManager::getYamlData("crate")["items"]);
        Horizon::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateItemShiftTask($inv, $items, static function (Item $item) use ($p, $inv) {
            $p->removeAllWindows();
            // $p->getInventory()->addItem($item);
            $tmp = new TempFloatingItem($p, $item, 5);
            $pos = $tmp->calculateRelativePosition($p);
            $cx = $pos->getX();
            $cz = $pos->getZ();
            $radius = 1;
            for ($i = 0; $i < 200; $i += 1.1) {
                $x = $cx + ($radius * cos($i));
                $z = $cz + ($radius * sin($i));
                $pos = new Vector3($x, $pos->getY(), $z);
                $p->getLevel()->addParticle(new LavaParticle($pos));
            }
            $p->addTitle("§aYou Won", $item->getName(), -1, 20 * 5, 0);
        }), 1);
    }

}