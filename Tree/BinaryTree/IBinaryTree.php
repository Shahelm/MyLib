<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 18:41
 */
namespace BinaryTree;

use BinaryTree\Exception\EmptyTreeException;
use BinaryTree\Exception\NoSuchElementException;
use BinaryTree\InsertStrategy\IInsertStrategy;
use BinaryTree\Node\INode;
use BinaryTree\Node\INodeComparator;

/**
 * Interface IBinaryTree
 *
 * @package BinaryTree
 */
interface IBinaryTree
{
    /**
     * @param INodeComparator $nodeComparator
     * @param IInsertStrategy $insertStrategy
     */
    public function __construct(INodeComparator $nodeComparator, IInsertStrategy $insertStrategy);
    
    /**
     * @param int $index
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function search($index);

    /**
     * @return INode
     *
     * @throws EmptyTreeException
     */
    public function getMinimum();

    /**
     * @return INode
     *
     * @throws EmptyTreeException
     */
    public function getMaximum();

    /**
     * @param INode $node
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function getPredecessor(INode $node);

    /**
     * @param INode $node
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function getSuccessor(INode $node);

    /**
     * @param INode $node
     */
    public function insert(INode $node);

    /**
     * @param INode $node
     *
     * @return bool
     */
    public function delete(INode $node);
}
