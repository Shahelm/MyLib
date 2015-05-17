<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.05.15
 * Time: 12:32
 */
namespace BinaryTree\InsertStrategy;

use BinaryTree\Node\INode;
use BinaryTree\Node\INodeComparator;

interface IInsertStrategy
{
    /**
     * @param INode $root
     * @param INode $newNode
     * @param INodeComparator $comparator
     *
     * @return void
     */
    public function insertNode(INode $root, INode $newNode, INodeComparator $comparator);
}
