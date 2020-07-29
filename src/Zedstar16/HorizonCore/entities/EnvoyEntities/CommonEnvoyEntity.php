<?php


namespace Zedstar16\HorizonCore\entities\EnvoyEntities;


use pocketmine\block\BlockFactory;
use pocketmine\entity\EntityIds;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class CommonEnvoyEntity extends BaseEnvoyEntity
{
    const TYPE_ID = EntityIds::FALLING_BLOCK;
    const HEIGHT = 0.98;

    public function __construct(Level $level, CompoundTag $nbt, Vector3 $spawnpos = null)
    {
        parent::__construct($level, $nbt, $spawnpos);
        if (!$this->namedtag->hasTag("BlockID", IntTag::class)) {
            $this->namedtag->setInt("BlockID", ItemIds::CHEST, true);
        }
        $this->getDataPropertyManager()->setInt(self::DATA_VARIANT, BlockFactory::toStaticRuntimeId($this->namedtag->getInt("BlockID")));
    }

    public function saveNBT(): void
    {
        parent::saveNBT();
        $this->namedtag->setInt("BlockID", $this->getDataPropertyManager()->getInt(self::DATA_VARIANT), true);
    }

}