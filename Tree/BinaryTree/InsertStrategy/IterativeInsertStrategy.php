<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.05.15
 * Time: 12:43
 */
namespace BinaryTree\InsertStrategy;

use BinaryTree\Node\INode;
use BinaryTree\Node\INodeComparator;

class IterativeInsertStrategy implements IInsertStrategy
{
    /**
     * @param INode $root
     * @param INode $newNode
     * @param INodeComparator $comparator
     *
     * @return void
     */
    public function insertNode(INode $root, INode $newNode, INodeComparator $comparator)
    {
        $newParent = null;

        while (null !== $root) {
            $newParent = $root;

            $root = $comparator->compare($newNode, $root) < 0 ? $root->getLeft() : $root->getRight();
        }

        $newNode->setParent($newParent);

        if ($comparator->compare($newNode, $newParent) < 0) {
            $newParent->setLeft($newNode);
        } else {
            $newParent->setRight($newNode);
        }
    }
}
