<?php

/*
 *
 *  ___________            _
 * |___  /  _  \          | |
 *    / /| | | |_   _  ___| |___
 *   / / | | | | | | |/ _ \ / __|
 * ./ /__| |/ /| |_| |  __/ \__ \
 * \_____/___/  \__,_|\___|_|___/
 *
 * @author Zedstar16
 *
 */


namespace Zedstar16\HorizonCore\components;

class Constants
{

    public const KIT_NORMAL = 0;
    public const KIT_FFA = 1;
    public const KIT_DUELS = 2;

    public static $kit = [
        self::KIT_NORMAL => "kits",
        self::KIT_FFA => "ffa",
        self::KIT_DUELS => "duels"
    ];

    public static $ffa_arenas = [
        "BuildPvP",
        "NoDebuff",
        "Gapple",
        "Combo",
        "Fist"
    ];


    public static $enchantment_by_id = [
        0 => "Protection",
        1 => "Fire_Protection",
        2 => "Feather_Falling",
        3 => "Blast_Protection",
        4 => "Projectile_Protection",
        5 => "Thorns",
        6 => "Respiration",
        7 => "Depth Strider",
        8 => "Aqua Affinity",
        9 => "Sharpness",
        10 => "Smite",
        11 => "Bane_of_Arthropods",
        12 => "Knockback",
        13 => "Fire_Aspect",
        14 => "Looting",
        15 => "Efficiency",
        16 => "Silk_Touch",
        17 => "Unbreaking",
        18 => "Fortune",
        19 => "Power",
        20 => "Punch",
        21 => "Flame",
        22 => "Infinity",
        23 => "Luck_of_the_Sea",
        24 => "Lure",
        25 => "Frost Walker",
        26 => "Mending",
        27 => "Binding" ,
        28 => "Vanishing",
        29 => "Impaling",
        30 => "Riptide",
        31 => "Loyalty",
        32 => "Channeling",
    ];


}