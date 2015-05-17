<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.05.15
 * Time: 12:45
 */
namespace BinaryTree\InsertStrategy;

use BinaryTree\Node\INode;
use BinaryTree\Node\INodeComparator;

class RecursiveInsertStrategy implements IInsertStrategy
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
        $result = $comparator->compare($root, $newNode);
        
        /**
         * The root element is less than or equal to the new node, then add in the right branch.
         */
        if ($result <= 0) {
            if (false === $root->hasRight()) {
                $root->setRight($newNode);
                $newNode->setParent($root);
            } else {
                $this->insertNode($root->getRight(), $newNode, $comparator);
            }
        } else {
            /**
             * The root element is greater than to the new element, then add in the left branch.
             */
            if (false === $root->hasLeft()) {
                $root->setLeft($newNode);
                $newNode->setParent($root);
            } else {
                $this->insertNode($root->getLeft(), $newNode, $comparator);
            }
        }
    }
}
