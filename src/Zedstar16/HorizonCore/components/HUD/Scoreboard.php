<?php


namespace Zedstar16\HorizonCore\components\HUD;


use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\JackMD\ScoreFactory\ScoreFactory;

class Scoreboard
{
    /** @var HorizonPlayer */
    private $p;

    private $lines = [];

    private $titles = [
        "§6Horizon§cPE",
        "§cH§6orizon§cPE",
        "§6H§co§6rizon§cPE",
        "§6Ho§cr§6izon§cPE",
        "§6Hor§ci§6zon§cPE",
        "§6Hori§cz§6on§cPE",
        "§6Horiz§co§6n§cPE",
        "§6Horizo§cn§cP§cE",
        "§6Horizon§6P§cE",
        "§6Horizon§cP§6E",
    ];

    private $tick = 0;

    public function __construct(HorizonPlayer $player)
    {
        $this->p = $player;
        ScoreFactory::setScore($player, $this->titles[$this->tick]);
    }


    public function setLines(array $lines){
        $this->lines = $lines;
        $this->update();
    }

    public function setLine(Int $line, $string){
        $this->lines[$line] = $string;
        $this->update();
    }

    public function update(){
        if($this->tick > count($this->titles)){
            $this->tick = 0;
        }else $this->tick++;
        ScoreFactory::setScore($this->p, $this->titles[$this->tick]);
        foreach ($this->lines as $line => $string) {
            ScoreFactory::setScoreLine($this->p, $line, $string);
        }
    }
}