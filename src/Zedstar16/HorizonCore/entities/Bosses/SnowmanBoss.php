<?php


namespace Zedstar16\HorizonCore\entities\Bosses;


use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;
use Zedstar16\HorizonCore\Horizon;

class SnowmanBoss extends BossBase
{

    const NETWORK_ID = self::SNOW_GOLEM;
    public $width = 0.6;
    public $height = 2;
    public $main;
    public $coolDownAttackTimeCounter = 30;
    public $coolDownAttackTime = 30;
    public $canAttack = false;
    public $isJumping = false;
    public $jumpTime = 8;
    protected $gravity = 0.08;
    protected $drag = 0.02;

    public function getName(): string
    {
        return "Snowman";
    }

    public function initEntity(): void
    {
        $this->main = Horizon::getInstance();
        $health = mt_rand(1000, 1500);
        $this->setMaxHealth($health);
        $this->getDataPropertyManager()->setFloat(Entity::DATA_SCALE, 2.5);
        $this->setNameTagAlwaysVisible(true);
        parent::initEntity();
    }

    public function onUpdate(int $currentTick): bool
    {
        if ($this->closed) {
            return false;
        }
        $tickDiff = $currentTick - $this->lastUpdate;
        $this->lastUpdate = $tickDiff;
        $this->entityBaseTick($tickDiff);

        if ($this->isAlive()) {
            $target = $this->getTargetEntity();
            if ($target == null || !$target instanceof Player) {
                $this->findTarget();
                return true;
            }

            $this->setNameTag("§f§lSnowman §6BOSS§r\n§c" . $this->getHealth() . "§a HP");

            $x = $target->x - $this->x;
            $y = $target->y - $this->y;
            $z = $target->z - $this->z;

            // ROTATION
            $this->yaw = rad2deg(atan2(-$x, $z));
            $this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

            // INCREASE BOSS RADIUS
            if ($this->distance($target) <= 15) {
                if ($x * $x + $z * $z > 1) {
                    $this->motion->x = 2.2 * 0.2 * ($x / (abs($x) + abs($z)));
                    $this->motion->z = 2.2 * 0.2 * ($z / (abs($x) + abs($z)));
                } else {
                    $this->motion->x = 0;
                    $this->motion->z = 0;
                }
            } else {
                $this->setTargetEntity(null);
                $this->motion->x = 0;
                $this->motion->z = 0;
            }


            if ($this->isCollidedHorizontally) {
                if (!$this->isJumping) {
                    $this->isJumping = true;
                }
            }

            if ($this->isJumping) {
                $this->motion->y = $this->gravity * 2;
                $this->jumpTime--;
                if ($this->jumpTime <= 0) {
                    $this->jumpTime = 8;
                    $this->isJumping = false;
                }
            }

            if ($this->isOnGround()) {
                // ??
                // $this->motion->y -= $this->gravity;
            } else {
                if (!$this->isJumping) {
                    $this->motion->y = -$this->gravity * 9;
                    $this->motion->x = 0;
                    $this->motion->z = 0;
                }
            }

            if ($this->distance($target) <= 15 && $this->canAttack == true) {
                $nbt = Entity::createBaseNBT(
                    $this->add(0, $this->getEyeHeight() + 1, 0),
                    $this->getDirectionVector(),
                    ($this->yaw > 180 ? 360 : 0) - $this->yaw,
                    -$this->pitch
                );
                $entity = Entity::createEntity("Snowball", $this->getLevel(), $nbt, $this);
                if ($entity instanceof Projectile) {
                    $entity->setBaseDamage(8);
                    $entity->spawnToAll();
                    $entity->setMotion($entity->getMotion()->multiply(1.8));
                }
                $this->canAttack = false;
                foreach ($this->main->getServer()->getOnlinePlayers() as $player) {
                    if ($target !== $player) {
                        if ($this->distance($target) <= 3) {
                            $player->attack(new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 6));
                            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 125, 1, true));

                        }
                    }
                }
            }

            $this->move($this->motion->x, $this->motion->y, $this->motion->z);
            $this->updateMovement();
        }

        return true;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $this->coolDownAttackTimeCounter--;
        if ($this->coolDownAttackTimeCounter <= 0) {
            $this->coolDownAttackTimeCounter = $this->coolDownAttackTime;
            $this->canAttack = true;
        }
        return $hasUpdate;
    }

    public function findTarget()
    {
        $target = null;
        $lastSquare = -1;
        foreach ($this->main->getServer()->getOnlinePlayers() as $player) {
            $square = $this->distance($player);
            if ($lastSquare === -1 or $lastSquare > $square) {
                $target = $player;
                $lastSquare = $square;
            }
        }
        if ($target != null) {
            $this->setTargetEntity($target);
        }
    }

    // TODO

    protected function sendSpawnPacket(Player $player): void
    {
        parent::sendSpawnPacket($player);
        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->item = Item::get(ItemIds::SNOWBALL);
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = 0;
        $player->dataPacket($pk);
    }

}