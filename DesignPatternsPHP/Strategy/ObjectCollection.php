<?php

namespace MyLib\DesignPatterns\Strategy;

/**
 * Class ObjectCollection
 */
class ObjectCollection
{
    /**
     * @var array
     */
    private $elements;

    /**
     * @var ComparatorInterface
     */
    private $comparator;

    /**
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        $this->elements = $elements;
    }

    /**
     * @return array
     */
    public function sort()
    {
        $callback = array($this->comparator, 'compare');
        uasort($this->elements, $callback);

        return $this->elements;
    }

    /**
     * @param ComparatorInterface $comparator
     *
     * @return void
     */
    public function setComparator(ComparatorInterface $comparator)
    {
        $this->comparator = $comparator;
    }
}