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
        $this->msg = $message;
    }

    public function getCleanedMessage(): string
    {
        $result = "there is an array which has some words";

        $bad = array("array", "of", "bad", "words");
        foreach ($bad as $vb) {
            $swear = "";
            foreach (str_split($vb) as $inx => $letter) {
                if ($inx) {
                    $swear .= "(\W*|\w){0,3}" . $letter;
                } else {
                    $swear .= $letter;
                }
            }
            $result = preg_replace("/$swear/i", "hi", $result);
        }

        $string = "you are a fu(kking cunt kys";
        $char_list = str_split("abcdefghijklmnopqrstuvwxyz1234567890-/:;()£&@“–—•₽¥¢€₩§„»«…¿¡'’‘`[]{}#‰%^*+≠≈_|~<>•");
        $split = str_split($string);
        $possibles = [
            "a" => ["4"],
            "i" => ["!", "¡", "|"],
            "o" => ["0", "()", "•", "<>"],
            "e" => ["3", "€"],
            "c" => ["(", "<", "[", "{"]
        ];
        $letters = [];
        foreach ($char_list as $chars) {

        }

        foreach ($split as $char) {
            if (!in_array($char, $char_list, true)) {
                $this->cansend = false;
                return "";
            }
            foreach ($possibles as $key => $possible) {
                foreach ($possible as $replacement) {
                    $this->checkStandardSwear(str_replace($replacement, $possible, $this->msg));

                }
            }
        }
        return "";
    }

    private function checkStandardSwear(string $string): bool
    {
        $bool = false;
        $bad = array("array", "of", "bad", "words");
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