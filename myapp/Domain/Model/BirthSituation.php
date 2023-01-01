<?php

namespace Myapp\Domain\Model;

class BirthSituation extends BaseModel
{

    //順番変更不可
    public static $columns = [
        '逆子', '吸引分娩', '鉗子分娩', '無痛分娩', '早産', '遅産', '帝王切開', '陣痛促進剤', 'へその緒が首に巻きついていた'
    ];
}
