<?php

namespace Myapp\Domain\Model;

class ShugiSkill extends BaseModel
{

    //////////////////////////////////////////////////////////
    ////////////////        props        ////////////////////
    /////////////////////////////////////////////////////////

    public $name = "";

    public $direction_1 = [];
    public $direction_2 = []; //通常方向は 垂直+9
    public $direction_3 = [];
    public $direction_4 = [];
    public $direction_5 = []; //通常方向は 垂直+3
    public $direction_6 = [];
    public $direction_7 = [];
    public $direction_8 = [];

    public function __construct($_name, $_1, $_2, $_3, $_4, $_5, $_6, $_7, $_8)
    {
        $this->name = $_name;
        $this->direction_1 = $_1;
        $this->direction_2 = $_2;
        $this->direction_3 = $_3;
        $this->direction_4 = $_4;
        $this->direction_5 = $_5;
        $this->direction_6 = $_6;
        $this->direction_7 = $_7;
        $this->direction_8 = $_8;
    }

    public static function getArray(
        $_name,
        $_1,
        $_2,
        $_3,
        $_4,
        $_5,
        $_6,
        $_7,
        $_8
    ) {

        $obj = new ShugiSkill($_name, $_1, $_2, $_3, $_4, $_5, $_6, $_7, $_8);

        return json_decode(json_encode($obj), true);
    }

    //////////////////////////////////////////////////////////
    ////////////////        pattern        //////////////////
    /////////////////////////////////////////////////////////

    public static $nothing_direction = [
        'first' => [0],
        'direction' =>  [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ];

    public static $direction__1_2 = [
        'first' => [99],
        'direction' =>  [1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ];

    // 4 ~ 6
    public static $direction__4_6 = [
        'first' => [99],
        'direction' => [0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0],
    ];

    // 4 ~ 8
    public static $direction__4_8 = [
        'first' => [99],
        'direction' => [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0],
    ];

    // 10 ~ 2
    public static $direction__10_2 = [
        'first' => [99],
        'direction' => [1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1]
    ];

    // 11 ~ 1
    public static $direction__11_1 = [
        'first' => [99],
        'direction' =>  [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
    ];

    //////////////////////////////////////////////////////////
    ////////////////        utils           //////////////////
    /////////////////////////////////////////////////////////

    private static function getBetweenDirection($start, $end)
    {

        $out_put =  [
            'first' => [99],
            'direction' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        $out_put['direction'][$start - 1] = 1;
        $out_put['direction'][$end - 1] = 1;

        return $out_put;
    }

    private static function getBetweenDirectionForInner($inner_direction, $start, $end)
    {

        $out_put =  [
            'first' => [99, $inner_direction],
            'direction' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        $out_put['direction'][$start - 1] = 1;
        $out_put['direction'][$end - 1] = 1;

        return $out_put;
    }

    private static function getInnerDirection($inner_direction, $_target_direction)
    {
        array_push($_target_direction['first'], $inner_direction);
        return $_target_direction;
    }

    private static function getNormalDirection($inner_direction = null)
    {

        $out_put =  [
            'first' => [99],
            'direction' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        if ($inner_direction != null) {
            array_push($out_put['first'], $inner_direction);
        }

        return $out_put;
    }

    private static function getAllDirection($inner_direction = null)
    {

        $out_put =  [
            'first' => [99],
            'direction' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
        ];

        if ($inner_direction != null) {
            array_push($out_put['first'], $inner_direction);
        }

        return $out_put;
    }

    private static function getMonoDirection($target_direction)
    {

        $out_put =  [
            'first' => [99],
            'direction' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        $out_put['direction'][$target_direction - 1] = 1;


        return $out_put;
    }


    private static function getMonoDirectionForInner($inner_direction, $target_direction)
    {

        $out_put =  [
            'first' => [99, $inner_direction],
            'direction' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        $out_put['direction'][$target_direction - 1] = 1;

        return $out_put;
    }


    //////////////////////////////////////////////////////////
    ////////////////        技リスト           ////////////////
    /////////////////////////////////////////////////////////

    public static $skill_name_List = [
        [
            "en" => "digestion",
            "ja" => "消化"
        ],
        [
            "en" => "blood",
            "ja" => "循環"
        ],
        [
            "en" => "breathing",
            "ja" => "呼吸"
        ],
        [
            "en" => "nerve",
            "ja" => "神経"
        ],
        [
            "en" => "urinary",
            "ja" => "泌尿"
        ],
        [
            "en" => "hormone",
            "ja" => "ホルモン"
        ],
        [
            "en" => "endocrine",
            "ja" => "内分泌"
        ],
        [
            "en" => "metabolism",
            "ja" => "代謝"
        ],
        [
            "en" => "reproduction",
            "ja" => "生殖器"
        ],
        [
            "en" => "immunity",
            "ja" => "免疫"
        ],
        [
            "en" => "lymph",
            "ja" => "リンパ"
        ],
        [
            "en" => "upper_limbs",
            "ja" => "上肢"
        ],
        [
            "en" => "lower_Limbs",
            "ja" => "下肢"
        ],
        [
            "en" => "exercise",
            "ja" => "運動"
        ],
        [
            "en" => "mycological",
            "ja" => "菌"
        ],
        [
            "en" => "heredity",
            "ja" => "遺伝"
        ]
    ];

    public static function getSkillList()
    {
        $skillLists = [];

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "消化",
                self::getNormalDirection(),
                self::$nothing_direction,
                self::$nothing_direction,
                self::$direction__11_1,
                self::$nothing_direction,
                self::getNormalDirection(),
                self::$nothing_direction,
                self::$nothing_direction
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "循環",
                self::$direction__11_1,
                self::getInnerDirection(9, self::$direction__11_1),
                self::$nothing_direction,
                self::$direction__1_2,
                self::getInnerDirection(3, self::$direction__1_2),
                self::$nothing_direction,
                self::$nothing_direction,
                self::$nothing_direction
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "呼吸",
                self::$direction__11_1,
                self::$direction__10_2,
                self::$nothing_direction,
                self::$nothing_direction,
                self::$direction__10_2,
                self::$direction__11_1,
                self::$direction__10_2,
                self::$direction__10_2
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "神経",
                self::getAllDirection(),
                self::getAllDirection(9),
                self::getAllDirection(),
                self::getAllDirection(),
                self::getAllDirection(3),
                self::getAllDirection(),
                self::getAllDirection(),
                self::getAllDirection()
            )
        );


        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "泌尿",
                self::$nothing_direction,
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(3),
                self::$nothing_direction,
                self::$nothing_direction,
                self::$nothing_direction
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "ホルモン",
                self::getNormalDirection(),
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(2),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection()
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "内分泌",
                self::getNormalDirection(),
                self::$nothing_direction,
                self::getMonoDirection(10),
                self::$nothing_direction,
                self::$nothing_direction,
                self::getMonoDirection(5),
                self::$nothing_direction,
                self::$nothing_direction
            )
        );


        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "代謝",
                self::$nothing_direction,
                self::$nothing_direction,
                self::getMonoDirection(10),
                self::$nothing_direction,
                self::$nothing_direction,
                self::getMonoDirection(5),
                self::$nothing_direction,
                self::$nothing_direction
            )
        );


        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "生殖器",
                self::$nothing_direction,
                self::getBetweenDirectionForInner(9, 5, 7),
                self::getBetweenDirection(6, 7),
                self::getBetweenDirection(5, 6),
                self::getBetweenDirectionForInner(3, 5, 7),
                self::$nothing_direction,
                self::$nothing_direction,
                self::$nothing_direction
            )
        );


        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "免疫",
                self::getNormalDirection(),
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(3),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection()
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "リンパ",
                self::getNormalDirection(),
                self::getMonoDirectionForInner(9, 6),
                self::$direction__4_6,
                self::$nothing_direction,
                self::getMonoDirectionForInner(3, 5),
                self::$nothing_direction,
                self::$nothing_direction,
                self::$nothing_direction
            )
        );


        //その他
        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "上肢",
                self::$direction__10_2,
                self::getInnerDirection(9, self::$direction__10_2),
                self::$nothing_direction,
                self::$nothing_direction,
                self::getInnerDirection(3, self::$direction__10_2),
                self::$direction__10_2,
                self::$direction__10_2,
                self::$direction__10_2,
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "下肢",
                self::$nothing_direction,
                self::getInnerDirection(9, self::$direction__4_8),
                self::$direction__4_8,
                self::getInnerDirection(3, self::$direction__4_8),
                self::$direction__4_8,
                self::$nothing_direction,
                self::$nothing_direction,
                self::$nothing_direction,
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "運動",
                self::getNormalDirection(),
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(3),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection()
            )
        );

        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "菌",
                self::getNormalDirection(),
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(3),
                self::getNormalDirection(),
                self::$nothing_direction,
                self::$nothing_direction
            )
        );


        array_push(
            $skillLists,
            ShugiSkill::getArray(
                "遺伝",
                self::getNormalDirection(),
                self::getNormalDirection(9),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection(3),
                self::getNormalDirection(),
                self::getNormalDirection(),
                self::getNormalDirection()
            )
        );



        return $skillLists;
    }
}
