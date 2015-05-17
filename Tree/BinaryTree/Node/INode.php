<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 18:30
 */
namespace BinaryTree\Node;

/**
 * Interface INode
 *
 * @package BinaryTree
 */
interface INode
{
    /**
     * @return INode|null
     */
    public function getLeft();

    /**
     * @return bool
     */
    public function hasLeft();

    /**
     * @param INode|null $left
     *
     * @return void
     */
    public function setLeft($left);
    
    /**
     * @return INode|null
     */
    public function getRight();

    /**
     * @return bool
     */
    public function hasRight();

    /**
     * @param INode|null $right
     *
     * @return void
     */
    public function setRight($right);
    
    /**
     * @return INode|null
     */
    public function getParent();

    /**
     * @param INode|null $parent
     *
     * @return void
     */
    public function setParent($parent);
    
    /**
     * @return bool
     */
    public function hasParent();
    
    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return bool
     */
    public function isLeft();

    /**
     * @return bool
     */
    public function isRight();
    
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getIndex();

    /**
     * @param int $index
     *
     * @return void
     */
    public function setIndex($index);

    /**
     * Depending on who is the node right or left leaf
     *
     * @param INode $node
     *
     * @return void
     */
    public function replaceChildNode(INode $node);
    
    /**
     * For example: for clearing of references
     *
     * @return void
     */
    public function clear();
}
