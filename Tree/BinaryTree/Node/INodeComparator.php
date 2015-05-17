<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 21:28
 */
namespace BinaryTree\Node;

/**
 * Interface INodeComparator
 *
 * @package BinaryTree
 */
interface INodeComparator
{
    /**
     * @param INode $a
     * @param INode $b
     *
     * @return int
     */
    public function compare(INode $a, INode $b);
}
