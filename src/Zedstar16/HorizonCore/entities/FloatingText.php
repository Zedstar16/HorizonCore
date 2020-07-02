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
        $this->setScale(0.01);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag($text);
        $this->namedtag->setString($tag, $tag);
        parent::__construct($level, $nbt);
    }

    public function setText(string $text)
    {
        $this->setNameTag($text);
    }

}