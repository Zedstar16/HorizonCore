<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Zedstar16\HorizonCore\entities\FloatingText;

class FloatingTextManager
{

    /** @var FloatingText[]  */
    public static $texts = [];

    public static function add(Level $level, Vector3 $pos, $tag, $text){
        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
        self::$texts[] = new FloatingText($level, $nbt, $text, $tag);
    }

    public static function set($tag, $text) : bool{
        $entity = self::getTag($tag);
        if($entity !== null){
            $entity->setNameTag($text);
            return true;
        }
        return false;
    }

    public static function remove($tag) : bool{
        $entity = self::getTag($tag);
        if($entity !== null){
            $entity->flagForDespawn();
            unset(array_keys(self::$texts, $entity)[0]);
            return true;
        }
        return false;
    }

    public static function getTag($tag) : ?FloatingText{
        foreach (self::$texts as $text){
            if($text->namedtag->hasTag($tag)){
                return $text;
            }
        }
        return null;
    }


}