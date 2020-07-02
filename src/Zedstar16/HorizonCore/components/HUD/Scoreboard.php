<?php


namespace Zedstar16\HorizonCore\components\HUD;


use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\JackMD\ScoreFactory\ScoreFactory;

class Scoreboard
{
    /** @var HorizonPlayer */
    private $p;

    private $lines = [];

    private $map = [];

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

    public function setScoreboard(BaseScoreboard $scoreboard)
    {
        ScoreFactory::removeScore($this->p);
        ScoreFactory::setScore($this->p, $this->titles[$this->tick]);
        ScoreFactory::setScoreLine($this->p, 1, str_repeat("§7–§7", $this->getLongestLineLength($scoreboard)));
        $lines = $scoreboard->getLines();
        // This is to make sure the longest line in the scorehud has a space at the end
        // This means it will not touch the end line number
        $longest = ["value" => 0];
        foreach ($lines as $key => $value) {
            $len = strlen($key . $value);
            if ($len > $longest["value"]) {
                $longest["value"] = $len;
                $longest["key"] = $key;
            }
        }
        $lines[$longest["key"]] .= " ";
        $i = 1;
        foreach ($lines as $key => $value) {
            $this->map[$key] = $i;
            $i++;
            ScoreFactory::setScoreLine($this->p, $i, "§c> §6$key: §e$value");
        }
        ScoreFactory::setScoreLine($this->p, $i + 1, str_repeat("§7–", $this->getLongestLineLength($scoreboard)));
    }

    public function updateLine($key, $value)
    {
        $line = $this->map[$key] ?? null;
        if ($line !== null) {
            $this->tick++;
            if (!isset($this->titles[$this->tick])) {
                $this->tick = 0;
            }
            ScoreFactory::setScoreLine($this->p, $line, "§c> §6$key: §e$value");
        }
    }

    public function getLongestLineLength(BaseScoreboard $scoreboard): int
    {
        $longest = 0;
        foreach ($scoreboard->getLines() as $key => $value) {
            $len = strlen($key . $value);
            if ($len > $longest) {
                $longest = $len;
            }
        }
        return $longest + 3;
    }


}