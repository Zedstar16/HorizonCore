<?php


namespace Zedstar16\HorizonCore\components\ChatHandler;


class ChatFilter
{

    /** @var String */
    public $msg;

    public $cansend = true;

    public $swear_found = false;

    public function __construct(string $message)
    {
        $this->msg = strtolower($message);
    }

    public function getCleanedMessage(): string
    {
        $i = 0;
        $bool = false;
        $time = microtime(true);
        $char_list = str_split("abcdefghijklmnopqrstuvwxyz1234567890-/:;()£&@“–—•₽¥¢€₩§„»«…¿¡'’‘`[]{}#‰%^*+≠≈_|~<>• ");
        $split = str_split($this->msg);
        $possibles = [
            "a" => ["4", "@"],
            "i" => ["!", "¡", "|", "1", "l", "/", "\\"],
            "o" => ["0", "()", "•", "<>"],
            "e" => ["3", "€"],
            "c" => ["(", "<", "[", "{"],
            "g" => ["q", "9", "6", "k", "b"],
            "s" => ["5"],
            "b" => ["8"]
        ];
        foreach ($split as $char) {
            if (!in_array($char, $char_list, true)) {
                $this->cansend = false;
                return "";
            }
            foreach ($possibles as $key => $possible) {
                foreach ($possible as $replacement) {
                    $this->checkStandardSwear(str_replace($replacement, $key, $this->msg));
                }
            }
        }
        return round(microtime(true) - $time, 5) * 1000;
    }

    private function checkStandardSwear(string $string): bool
    {
        $bool = false;
        $bad = ["cunt", "fuck", "shit", "nigga", "kys", "nigger", "retard"];
        foreach ($bad as $vb) {
            $swear = "";
            foreach (str_split($vb) as $inx => $letter) {
                if ($inx) {
                    $swear .= "(\W*|\w){0,3}" . $letter;
                } else {
                    $swear .= $letter;
                }
            }
            if (strpos(preg_replace("/$swear/i", "‰", $string), "‰") !== false) {
                $bool = true;
                $this->swear_found = true;
            }
        }
        return $bool;
    }
}