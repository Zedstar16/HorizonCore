<?php


namespace Zedstar16\HorizonCore\components\Crate;


use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\TempFloatingItem;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\utils\Utils;

class Crate
{

    private $name;
    /** @var Item[] */
    private $items;

    private $pos;

    public function __construct(string $name, array $contents, Position $pos = null)
    {
        $this->name = $name;
        $this->items = $contents;
        $this->pos = $pos;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContents()
    {
        return $this->items;
    }

    public function open(Player $player)
    {
        $inv = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv->setName(Utils::colorize($this->name) . " Crate");
        $inv->readonly(true);
        $inv->send($player);
        $items = $this->items;
        Horizon::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateItemShiftTask($inv, $items, static function (Item $item) use ($player, $inv) {
            $player->removeAllWindows();
            $player->getInventory()->addItem($item);
            $tmp = new TempFloatingItem($player, $item, 5);
            $player->addTitle("§aYou Won", $item->getName(), -1, 20 * 5);
        }), 1);

    }


}