<?php
$arr = range($argv[1], $argv[2]);
shuffle($arr);

print_r($arr);

Class BubbleSort
{
    private $_arr;

    private $_amount;

    private $_sortType;

    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';


    public function __construct($arr, $sortType = self::SORT_ASC)
    {
        $this->_arr = $arr;

        $this->_amount = count($arr);

        if ($sortType == self::SORT_ASC || $sortType == self::SORT_DESC) {
            $this->_sortType = $sortType;
        }

    }

    private function _swap(&$a, &$b)
    {
        list($a, $b) = array($b, $a);
    }

    private function _sort()
    {
        for ($i = 0; $i < $this->_amount-1; $i++) {

            if ($this->_compared($this->_arr[$i], $this->_arr[$i+1])) {
                $this->_swap($this->_arr[$i], $this->_arr[$i+1]);
            }
        }
    }

    private function _compared($a, $b)
    {
        if ($this->_sortType == self::SORT_ASC) {
            if ($a > $b) {
                $return =  true;
            }
        } elseif ($this->_sortType == self::SORT_DESC) {
            if ($a < $b) {
                $return = true;
            }
        }

        return isset($return) ? $return : false;
    }

    public function sort()
    {
        for ($i = 0; $i < $this->_amount; $i++) {
            $this->_sort($this->_arr);
        }

        return $this->_arr;
    }
}

if ($argv[3] == 'asc' ) {
    $sortType = BubbleSort::SORT_ASC;
} else {
    $sortType =  BubbleSort::SORT_DESC;
}

$bubbleSort = new BubbleSort($arr, $sortType);
print_r($bubbleSort->sort());

