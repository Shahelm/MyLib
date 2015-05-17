<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 21:30
 */
namespace BinaryTree\Node;

/**
 * Class NodeComparator
 *
 * @package BinaryTree
 */
class NodeComparator implements INodeComparator
{
    /**
     * @param INode $a
     * @param INode $b
     *
     * @return int
     */
    public function compare(INode $a, INode $b)
    {
        return $a->getIndex() - $b->getIndex();
    }
}
