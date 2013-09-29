<?php

$arr = range($argv[1], $argv[2]);

shuffle($arr);

class SelectionSort
{
    private $array;

    private $count;

    private $sortType;

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    public function __construct($arr, $sortType)
    {
        $this->array = $arr;
        $this->count = count($arr);
        $this->sortType = $sortType == self::SORT_ASC ? self::SORT_ASC : self::SORT_DESC;
    }

    public function sort()
    {
        for ($i = 0; $i < $this->count-1; $i++) {
            if ($this->sortType == self::SORT_ASC) {
                $this->_sortAsc($i);
            } elseif ($this->sortType == self::SORT_DESC) {
                $this->_sortDesc($i);
            }
        }

        return $this->array;
    }

    private function _sortAsc($i)
    {
        $min = $i;

        for ($j = $i + 1; $j < $this->count; $j++ ) {

            if ($this->array[$j] < $this->array[$min]) {
                $min = $j;
            }
        }

        if ($min != $i) {
            $this->_swap($this->array[$i], $this->array[$min]);
        }
    }

    private function _sortDesc($i)
    {
        $max = $i;

        for ($j = $i + 1; $j < $this->count; $j++ ) {

            if ($this->array[$j] > $this->array[$max]) {
                $max = $j;
            }
        }

        if ($max != $i) {
            $this->_swap($this->array[$i], $this->array[$max]);
        }
    }

    private function _swap(&$a, &$b)
    {
        list($a, $b) = array($b, $a);
    }
}

if ($argv[3] == 'asc' ) {
    $sortType = SelectionSort::SORT_ASC;
} else {
    $sortType =  SelectionSort::SORT_DESC;
}

print_r($arr);

$SelectionSort = new SelectionSort($arr, $sortType);

print_r($SelectionSort->sort());


