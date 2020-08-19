<?php


namespace Zedstar16\HorizonCore\components\Crate;


use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\scheduler\Task;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\SharedInvMenu;

class CrateItemShiftTask extends Task
{

    public $i = 0;

    public $inv;

    public $index = [];

    public $current_selection = [];

    public $current_item;


    public $callable;

    public function __construct(SharedInvMenu $inv, array $items, ?callable $callable)
    {
        $this->inv = $inv;
        shuffle($items);
        $this->index = $items;
        $this->callable = $callable;
        for ($i = 0; $i <= 8; $i++) {
            $this->current_selection[] = $i;
        }
    }

    // NOTES
    // RANGE FOR ITEMS IN CHEST : SLOTS 9 - 17
    // RANGE FOR GLASS : 0-8 AND 18-26

    public function onRun(int $currentTick)
    {
        $this->i++;
        $i = $this->i;
        if ($i <= 40) {
            $this->shift();
        } elseif ($i <= 60) {
            if (is_int($i / 4)) { // 15
                $this->shift();
            }
        } elseif ($i <= 80) {
            if (is_int($i / 8)) { // 10
                $this->shift();
            }
        } elseif ($i <= 98) {
            if (is_int($i / 7)) { // 7
                $this->shift();
            }
        } elseif ($i <= 120) {
            if (is_int($i / 12)) { // 5
                $this->shift();
            }
        } elseif ($i <= 138) {
            if (is_int($i / 23)) { // 3
                $this->shift();
            }
        } elseif ($i <= 150) {
            if (is_int($i / 75)) { // 2
                $this->shift();
            }
        } elseif ($i <= 160) {
            if (is_int($i / 80)) { // 2
                $this->shift();
            }
        } elseif ($i === 170) {
            $callable = $this->callable;
            $callable($this->current_item);
            $contents = [];
            for ($i = 0; $i < 27; $i++) {
                $contents[$i] = ItemFactory::get(ItemIds::STAINED_GLASS_PANE, 8)->setCustomName(" ");
            }
            $contents[13] = $this->current_item;
            $this->inv->getInventory()->setContents($contents);
            $this->getHandler()->cancel();
        }
    }

    public function shift()
    {
        $contents = [];
        array_shift($this->current_selection);
        $last = $this->current_selection[count($this->current_selection) - 1];
        if ($last === count($this->index) - 1) {
            $this->current_selection[] = 0;
        } else {
            $this->current_selection[] = $this->current_selection[count($this->current_selection) - 1] + 1;
        }
        for ($i = 0; $i < 27; $i++) {
            if ($i < 10 || $i >= 17) {
                $contents[$i] = ItemFactory::get(ItemIds::STAINED_GLASS_PANE, 8)->setCustomName(" ");
            } else {
                if ($i === 13) {
                    $this->current_item = $this->index[$this->current_selection[$i - 10]];
                }
                $contents[$i] = $this->index[$this->current_selection[$i - 10]];
            }
        }
        $contents[4] = ItemFactory::get(ItemIds::END_ROD, 8)->setCustomName(" ");
        $contents[22] = ItemFactory::get(ItemIds::END_ROD, 8)->setCustomName(" ");
        $this->inv->getInventory()->setContents($contents);
    }

}