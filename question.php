<?php

class Poker
{
    public $legalPattern = [1, 2, 3, 4];

    public $legalNum = [1, 2, 3, 4, 5, 6, 7, 8, 9, 'T', 'J', 'Q', 'K'];

    private $cardType;

    public $recordNum;

    public $recordPattern;

    public $arr; // record each poker in array

    public $straightRec; // record for comparing straight

    private $count = 0;

    /**
     * 建構元
     * 
     * @param string | 牌組
     */
    public function __construct(string $str)
    {
        $this->arr = str_split($str, 2);

        $this->recordNum     = array_fill_keys($this->legalNum, 0);
        $this->recordPattern = array_fill_keys($this->legalPattern, 0);

        $this->validateInputAndInit($this->arr);

        if (strlen($str) == 10) { // 5張
            $this->setCardType($this->arr);
        } elseif (strlen($str) == 26) { // 13張
            $this->set13CardType($this->arr);
        } else {
            throw new Exception('牌數並非合理輸入範圍，請重新輸入');
        }
    }

    /**
     * 取得輸出的牌組的牌型
     * 
     * @return string | 牌型名稱
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * 判斷牌型並設值
     * 
     * @return string｜牌型名稱
     */
    public function setCardType()
    {
        if ($this->isStraight()) {
            if ($this->isFlush()) {
                $this->cardType = '同花順';
            } else {
                $this->cardType = '順子';
            }
        } elseif ($this->isFourOfAKind()) {
            $this->cardType = '鐵支';
        } elseif ($this->isFullHouse()) {
            $this->cardType = '葫蘆';
        } elseif ($this->isFlush()) {
            $this->cardType = '同花';
        } else {
            $this->cardType = '散牌';
        }
    }

    /**
     * 判斷13支牌型並設值
     * 
     * @return string｜牌型名稱
     */
    public function set13CardType()
    {
        if ($this->isAllBig()) {
            $this->cardType .= '全大 ';
        } elseif ($this->isAllSmall()) {
            $this->cardType .= '全小 ';
        }

        if ($this->isThreeFlush()) {
            $this->cardType .= '三同花 ';
        }

        if ($this->isThreeStraight()) {
            $this->cardType .= '三順子 ';
        }

        if ($this->isDragon()) {
            $this->cardType .= '一條龍 ';
        }

        if ($this->isStraight()) {
            if ($this->isFlush()) {
                $this->cardType .= '同花順 ';
            } else {
                $this->cardType .= '順子 ';
            }
        } 
        
        if ($this->isFourOfAKind()) {
            $this->cardType .= '鐵支 ';
        } 
        
        if ($this->isFullHouse()) {
            $this->cardType .= '葫蘆 ';
        } 
        
        if ($this->isFlush()) {
            $this->cardType .= '同花 ';
        } 
        if (empty($this->cardType)) {
            $this->cardType = '散牌';
        }
    }

    /**
     * 檢查輸入是否符合規則，並將輸入的牌統計其數字
     * 
     * @return null
     */
    public function validateInputAndInit($arr)
    {
        $temp = [];

        foreach ($arr as $value) {
            if (!in_array($value[0], $this->legalPattern)) {
                throw new Exception('花色並非合理輸入範圍，請重新輸入');
            } elseif (!in_array($value[1], $this->legalNum)) {
                throw new Exception('數字並非合理輸入範圍，請重新輸入');
            } elseif (isset($temp[$value])) {
                throw new Exception('有重複的牌及花色，請重新輸入');
            }

            $this->recordNum[$value[1]]++;
            $this->recordPattern[$value[0]]++;

            // 記錄牌和花色
            $temp[$value] = 0;
        }
    }

    /**
     * 是否為順子
     * 
     * @return boolean
     */
    public function isStraight()
    {
        $isCounting = 0;

        if (
            $this->recordNum['T'] >= 1 && 
            $this->recordNum['J'] >= 1 && 
            $this->recordNum['Q'] >= 1 && 
            $this->recordNum['K'] >= 1 && 
            $this->recordNum['1'] >= 1
        ) {
            $this->straightRec = ['T', 'J', 'Q', 'K', '1'];

            return true;
        }

        foreach ($this->recordNum as $value) {
            if ($value >= 1) {
                $isCounting++;

                $this->straightRec[] = key($this->recordNum);

                if ($isCounting == 5) {
                    return true;
                }
            } elseif ($value == 0) {
                $isCounting = 0;
            } 
        }

        return false;
    }

    /**
     * 是否為鐵支
     * 
     * @return boolean
     */
    public function isFourOfAKind()
    {
        if (in_array(4, $this->recordNum)) {
            return true;
        }

        return false;
    }

    /**
     * 是否為葫蘆
     * 
     * @return boolean
     */
    public function isFullHouse()
    {
        if (in_array(2, $this->recordNum) && in_array(3, $this->recordNum)) {
            return true;
        }
        
        return false;
    }

    /**
     * 是否為同花
     * 
     * @return boolean
     */
    public function isFlush()
    {
        foreach ($this->recordPattern as $value) {
            if ($value >= 5) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 是否為全大
     * 
     * @return boolean
     */
    public function isAllBig()
    {
        for ($i = 2; $i <= 8; $i++ ) { 
            $this->count += $this->recordNum[$i];
        }

        if ($this->count == 0) {
            return true;
        }

        return false;
    }

    /**
     * 是否為全小
     * 
     * @return boolean
     */
    public function isAllSmall()
    {
        if ($this->count == 13) {
            return true;
        }

        return false;
    }

    /**
     * 是否為三同花
     * 
     * @return boolean
     */
    public function isThreeFlush()
    {
        $temp = [0, 3, 5, 8, 10, 13];
        $bool = true;

        foreach ($this->recordPattern as $value) {
            if (!in_array($value, $temp)) {
                $bool = false;
                break;
            }
        }

        return $bool;
    }

    /**
     * 是否為三順子
     * 
     * @return boolean
     */
    public function isThreeStraight()
    {
        $countStraightNum = 0;
        $isCounting = 0;
        $tempRecordNum = $this->recordNum;

        for ($i = 0; $i < 3; $i++) { 
            $isCounting = 0;
            $index = [];

            if (
                $tempRecordNum['T'] >= 1 && 
                $tempRecordNum['J'] >= 1 && 
                $tempRecordNum['Q'] >= 1 && 
                $tempRecordNum['K'] >= 1 && 
                $tempRecordNum['1'] >= 1
            ) {
                $tempRecordNum['T']--;
                $tempRecordNum['J']--;
                $tempRecordNum['Q']--;
                $tempRecordNum['K']--;
                $tempRecordNum['1']--;

                $countStraightNum++;

                continue;
            }

            foreach ($tempRecordNum as $key => $value) {
                if ($value >= 1) {
                    $isCounting++;
                    $index[] = $key;
                    if ($isCounting == 5) {
                        $countStraightNum++;
                        foreach ($index as $value) {
                            $tempRecordNum[$value]--;
                        }
                    } elseif ($countStraightNum == 2 && $isCounting == 3) {
                        return true;
                    }
                } elseif ($value == 0) {
                    $isCounting = 0;
                } 
            }
        }

        return false;
    }

    /**
     * 是否為一條龍
     * 
     * @return boolean
     */
    public function isDragon()
    {
        $count = array_count_values($this->recordNum);

        if ($count[1] == 13) {
            return true;
        }

        return false;
    }
}


class Compare
{
    private $numSmallToBig = [3, 4, 5, 6, 7, 8, 9, 'T', 'J', 'Q', 'K', 1, 2];

    private $p1;

    private $p2;

    private $cpCartType = [
        '同花順' => 1,
        '鐵支'  => 2,
        '葫蘆'  => 3,
        '同花'  => 4,
        '順子'  => 5,
        '散牌'  => 6
    ];

    public function __construct(Poker $p1, Poker $p2)
    {
        $this->p1 = $p1;
        $this->p2 = $p2;
    }

    public function compare()
    {
        $operator = '=';

        // 先比牌型
        if ($this->cpCartType[$this->p1->getCardType()] < $this->cpCartType[$this->p2->getCardType()]) {
            $operator = '>';
        } elseif ($this->cpCartType[$this->p1->getCardType()] > $this->cpCartType[$this->p2->getCardType()]) {
            $operator = '<';
        } else {
            // 牌型一樣再另外比較
            switch ($this->p1->getCardType()) {
                case '同花順':
                    $operator = $this->cpStraightFlush();
                    break ;
                case '鐵支':
                    $operator = $this->cpFourOfAKind();
                    break;
                case '葫蘆':
                    $operator = $this->cpFullHouse();
                    break;
                case '同花':
                    $operator = $this->cpFlush();
                    break;
                case '順子':
                    $operator = $this->cpStraight();
                    break;
                case '散牌':
                    $operator = $this->cpHighCard();
                    break;
                default:
                    break;
            }
        }

        return '第一副（' . $this->p1->getCardType() . '）' . $operator . ' 第二副（' . $this->p2->getCardType() . '）' . "\n";
    }

    public function cpStraightFlush()
    {
        return $this->cpStraight(); // 如果兩者都是同花順，則 compare 方法跟 順子一樣
    }

    public function cpFourOfAKind()
    {
        $p1Num = array_search(4, $this->p1->recordNum);
        $p2Num = array_search(4, $this->p2->recordNum);

        return array_search($p1Num, $this->numSmallToBig) > array_search($p2Num, $this->numSmallToBig) ? '>' : '<';
    }

    public function cpFullHouse()
    {
        $p1Num = array_search(3, $this->p1->recordNum);
        $p2Num = array_search(3, $this->p2->recordNum);

        return array_search($p1Num, $this->numSmallToBig) > array_search($p2Num, $this->numSmallToBig) ? '>' : '<';
    }

    public function cpFlush()
    {
        $p1Pattern = array_search(5, $this->p1->recordPattern);
        $p2Pattern = array_search(5, $this->p2->recordPattern);

        if ($p1Pattern > $p2Pattern) {
            return '<';
        } elseif ($p1Pattern < $p2Pattern) {
            return '>';
        } else {
            return '=';
        }
    }

    public function cpStraight()
    {
        $p1 = $this->p1->straightRec;
        $p2 = $this->p2->straightRec;

        $p1Code = $this->setCode($p1);
        $p2Code = $this->setCode($p2);
        
        if ($p1Code < $p2Code) {
            return '<';
        } elseif ($p1Code > $p2Code) {
            return '>';
        } else {
            if ($p1Code == 2 || $p1Code == 0) {
                $p1CompareNum = reset($p1);
                $p2CompareNum = reset($p2);
            } else {
                if (end($p1) <> end($p2)) {
                    return array_search(end($p1), $this->numSmallToBig) > array_search(end($p2), $this->numSmallToBig) ? '>' : '<';
                }

                $p1CompareNum = end($p1);
                $p2CompareNum = end($p2);
            }

            $p1BiggestPattern = $this->getPatternByNum($this->p1->arr, $p1CompareNum);
            $p2BiggestPattern = $this->getPatternByNum($this->p2->arr, $p2CompareNum);

            return $p1BiggestPattern < $p2BiggestPattern ? '>' : '<';
        }
    }

    public function cpHighCard()
    {
        for ($i = 0, $j = 0; $i < 5 && $j < 5;) {
            if ($this->compareForeach($i, $j) == '>') {
                $j++;
            } else {
                $i++;
            };
        }

        return $i == 5 ? '<' : '>';
    }

    public function getPatternByNum(array $arr, int $num)
    {
        foreach ($arr as $value) {
            if ($value[1] == $num) {
                return $value[0];
            }
        }
    }

    public function compareForeach(int $i, int $j)
    {
        if (array_search($this->p1->arr[$i][1], $this->numSmallToBig) > array_search($this->p2->arr[$j][1], $this->numSmallToBig)) {
            return '>';
        } elseif (array_search($this->p1->arr[$i][1], $this->numSmallToBig) < array_search($this->p2->arr[$j][1], $this->numSmallToBig)) {
            return '<';
        } else {
            return $this->p1->arr[$i][0] > $this->p2->arr[$i][0] ? '<' : '>';
        }
    }

    /**
     * 設定一個代碼，2 為最大，0 最小，其他當１
     */
    public function setCode(array $arr)
    {
        if (end($arr) == 5) {
            return 0;
        } elseif (end($arr) == 6) {
            return 2;
        } else {
            return 1;
        }
    }
}

$poker8 = new Poker("11213141292T2J2Q2K3Q3K3T4K");
$poker9 = new Poker("1112131415161718191T1J1Q1K");
$poker10 = new Poker("11121314152122232425313233");
$poker11 = new Poker("12224333142435451626374718");

echo $poker8->getCardType() . "\n";
echo $poker9->getCardType() . "\n";
echo $poker10->getCardType() . "\n";
echo $poker11->getCardType() . "\n";

// $compare1 = new Compare(new Poker("1T1J1Q1K11"), new Poker("2425272836"));
// $compare2 = new Compare(new Poker("43231T2T13"), new Poker("4T1T2T3T1K"));
// $compare3 = new Compare(new Poker("1121314122"), new Poker("3222124245"));
// $compare4 = new Compare(new Poker("1222324424"), new Poker("4515252636"));
// $compare5 = new Compare(new Poker("222426282T"), new Poker("1113141517"));
// $compare6 = new Compare(new Poker("1122334445"), new Poker("1314252637"));
// $compare7 = new Compare(new Poker("2713142526"), new Poker("1T2J3Q3K11"));
// $compare8 = new Compare(new Poker("3132333435"), new Poker("2122232425"));
// $compare9 = new Compare(new Poker("122536481T"), new Poker("224518194Q"));

// echo $compare1->compare();
// echo $compare2->compare();
// echo $compare3->compare();
// echo $compare4->compare();
// echo $compare5->compare();
// echo $compare6->compare();
// echo $compare7->compare();
// echo $compare8->compare();
// echo $compare9->compare();
