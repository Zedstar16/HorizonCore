<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Throwable;
use Zedstar16\HorizonCore\entities\FloatingText;
use Zedstar16\HorizonCore\utils\Utils;

class FloatingTextManager
{

    /** @var array */
    public static $texts = [];
    /** @var Level[] */
    private static $levels = [];

    public static function isLoadedIn(Level $level)
    {
        return isset(self::$levels[$level->getName()]);
    }

    public static function loadIn(Level $level)
    {
        $data = FileManager::getJsonData("floating-texts");
        $exists = [];
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof FloatingText && $entity->namedtag->hasTag("floatingtext")) {
                $tag = $entity->namedtag->getString("floatingtext");
                if (!isset($data[$level->getName()][$tag])) {
                    $entity->flagForDespawn();
                } else {
                    if (!isset($exists[$tag])) {
                        self::$texts[$level->getName()][$entity->namedtag->getString("floatingtext")] = $entity;
                        $entity->setNameTag($data[$level->getName()][$tag]["text"]);
                    } else $entity->flagForDespawn();
                }
            }
        }
        if (isset($data[$level->getName()])) {
            $toadd = $data[$level->getName()];
            foreach ($toadd as $tag => $info) {
                foreach ($level->getEntities() as $entity) {
                    if ($entity instanceof FloatingText) {
                        if ($entity->namedtag->hasTag($tag)) {
                            unset($toadd[$tag]);
                        }
                    }
                }
            }
            foreach ($toadd as $tag => $info) {
                self::add($level, new Vector3($info["pos"]["x"], $info["pos"]["y"], $info["pos"]["z"]), $tag, $info["text"]);
            }
        }
    }

    public static function unload(Level $level)
    {
        if (isset(self::$texts[$level->getName()])) {
            unset(self::$texts[$level->getName()]);
        }
        if (isset(self::$levels[$level->getName()])) {
            unset(self::$levels[$level->getName()]);
        }
    }

    private static function save(Level $level, Vector3 $pos, $name, $text)
    {
        $data = FileManager::getJsonData("floating-texts");
        $data[$level->getName()][$name] = [
            "pos" => [
                "x" => $pos->x,
                "y" => $pos->y,
                "z" => $pos->z
            ],
            "text" => $text
        ];
        FileManager::saveJsonData("floating-texts", $data);
    }


    public static function add(Level $level, Vector3 $pos, $name, $text)
    {
        self::save($level, $pos, $name, $text);
        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
        $nbt->setString("floatingtext", $name);
        $entity = Entity::createEntity("FloatingText", $level, $nbt, $name, $text);
        $entity->spawnToAll();
        $entity->setNameTag($text);
        self::$texts[$level->getName()][] = $entity;
    }

    public static function update(FloatingText $entity, $tag, $text): bool
    {
        if ($entity !== null) {
            $data = FileManager::getJsonData("floating-texts");
            $data[$entity->getLevel()->getName()][$tag]["text"] = $text;
            FileManager::saveJsonData("floating-texts", $data);
            $entity->setNameTag($text);
            return true;
        }
        return false;
    }

    public static function remove($tag, ?Level $level = null): bool
    {
        try {
            $entity = self::getTag($tag, $level);
            if ($entity !== null) {
                $data = FileManager::getJsonData("floating-texts");
                $levelname = $entity->getLevel()->getName();
                $key = array_search($entity, self::$texts[$levelname], true);
                if (isset(self::$texts[$levelname][$key])) {
                    unset(self::$texts[$levelname][$key]);
                    $levelname = $entity->getLevel()->getName();
                    unset($data[$levelname][$tag]);
                    FileManager::saveJsonData("floating-texts", $data);
                    $entity->flagForDespawn();
                    return true;
                }
            }
        } catch (Throwable $err) {
            Utils::error($err);
        }
        return false;
    }

    public static function getTag($tag, ?Level $level = null): ?FloatingText
    {
        if (($level !== null) && isset(self::$texts[$level->getName()])) {
            foreach (self::$texts[$level->getName()] as $entity) {
                if (($entity !== null) && $entity->namedtag->hasTag($tag)) {
                    return $entity;
                }
            }
        } else {
            foreach (self::$texts as $levels) {
                foreach ($levels as $entity) {
                    if ($entity !== null && $entity->namedtag->hasTag($tag)) {
                        return $entity;
                    }
                }
            }
        }
        return null;
    }


}