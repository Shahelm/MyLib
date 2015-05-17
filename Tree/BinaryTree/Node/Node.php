<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 18:29
 */
namespace BinaryTree\Node;

/**
 * Class Node
 *
 * @package BinaryTree
 */
class Node implements INode
{
    /**
     * @var int
     */
    private $index;
    
    /**
     * @var INode|null;
     */
    private $left;
    
    /**
     * @var INode|null;
     */
    private $right;
    
    /**
     * @var INode|null;
     */
    private $parent;
    
    /**
     * @var mixed
     */
    private $value;

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return INode|null
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param INode|null $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return INode|null
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param INode|null $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return INode|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param INode|null $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return null === $this->getParent();
    }

    /**
     * @return bool
     */
    public function isLeft()
    {
        return false === $this->isRoot() && $this->getParent()->getLeft() === $this;
    }

    /**
     * @return bool
     */
    public function isRight()
    {
        return false === $this->isRoot() && $this->getParent()->getRight() === $this;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function hasLeft()
    {
        return null !== $this->getLeft();
    }

    /**
     * @return bool
     */
    public function hasRight()
    {
        return null !== $this->getRight();
    }
    
    /**
     * @return bool
     */
    public function hasParent()
    {
        return null !== $this->getParent();
    }

    /**
     * @param INode $newNode
     */
    public function replaceChildNode(INode $newNode)
    {
        if ($newNode->isLeft()) {
            $this->setLeft($newNode);
        } else {
            $this->setRight($newNode);
        }
    }

    /**
     * For example: for clearing of references
     *
     * @return void
     */
    public function clear()
    {
        $this->index = null;
        $this->left = null;
        $this->right = null;
        $this->parent = null;
        $this->value = null;
    }
}
