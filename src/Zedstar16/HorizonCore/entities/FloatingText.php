<?php


namespace Zedstar16\HorizonCore\entities;


use pocketmine\entity\Zombie;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class FloatingText extends Zombie
{

    public $text;

    public function __construct(Level $level, CompoundTag $nbt, $tag = "", $text = "")
    {
        parent::__construct($level, $nbt);
        $this->namedtag->setString($tag, $tag);
        $this->setScale(0.01);
        $this->setNameTagAlwaysVisible(true);
        $this->setInvisible(false);
        $this->setNameTag($text);
    }

    public function setText(string $text)
    {
        $this->setNameTag($text);
    }

}