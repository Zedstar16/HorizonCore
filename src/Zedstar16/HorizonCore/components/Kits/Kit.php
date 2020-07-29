<?php


namespace Zedstar16\HorizonCore\components\Kits;


use Zedstar16\HorizonCore\HorizonPlayer;

class Kit
{

    public $name;

    public $p;

    public function __construct(string $kitname, ?HorizonPlayer $player = null)
    {
        $this->name = $kitname;
        $this->p = $player;
    }

    public function setKit()
    {

    }

    public function getCooldown()
    {

    }


}