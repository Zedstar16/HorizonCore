<?php


namespace Zedstar16\HorizonCore\components;


use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use Zedstar16\HorizonCore\entities\FloatingItemEntity;
use Zedstar16\HorizonCore\Horizon;

class TempFloatingItem
{

    public $player;
    public $item;
    public $disappear_in;

    public function __construct(Player $player, Item $item, $disappear_in = 2)
    {
        $this->player = $player;
        $this->item = $item;
        $this->disappear_in = $disappear_in;
        $this->display();
    }

    private function display(): void
    {
        $position = $this->calculateRelativePosition($this->player);
        $nbt = Entity::createBaseNBT($position, null, lcg_value() * 360, 0);
        $itemTag = $this->item->nbtSerialize();
        $itemTag->setName("Item");
        $nbt->setShort("Health", 5);
        $nbt->setShort("PickupDelay", 999);
        $nbt->setTag($itemTag);
        $itemEntity = Entity::createEntity("FloatingItemEntity", $this->player->getLevel(), $nbt, $this->player);
        if ($itemEntity instanceof FloatingItemEntity) {
            $itemEntity->spawnTo($this->player);
            $itemEntity->entityBaseTick(0);
            $itemEntity->setNameTag($this->item->getName());
            $itemEntity->setNameTagVisible();
        }
        Horizon::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($itemEntity) : void {
            if (!$itemEntity->isClosed()) {
                $itemEntity->flagForDespawn();
                $this->player->getInventory()->addItem($this->item);
            }
        }), $this->disappear_in * 20);
    }

    public function calculateRelativePosition(Player $player): Vector3
    {
        $position = $player->asVector3();
        $direction = $player->getDirectionVector();
        $subtract = $direction->multiply(0.75);
        $position = $position->add($subtract);
        $position->y += ($player->getEyeHeight() - 0.2);
        return $position;
    }

}