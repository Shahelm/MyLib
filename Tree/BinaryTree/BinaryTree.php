<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.05.15
 * Time: 18:33
 */
namespace BinaryTree;

use BinaryTree\Exception\EmptyTreeException;
use BinaryTree\Exception\NoSuchElementException;
use BinaryTree\InsertStrategy\IInsertStrategy;
use BinaryTree\Node\INode;
use BinaryTree\Node\INodeComparator;

class BinaryTree implements IBinaryTree, IExtendedBinaryTree
{
    /**
     * @var INode
     */
    private $root;
    
    /**
     * @var INodeComparator
     */
    private $nodeComparator;
    
    /**
     * @var IInsertStrategy
     */
    private $insertStrategy;

    /**
     * @param INodeComparator $nodeComparator
     * @param IInsertStrategy $insertStrategy
     */
    public function __construct(INodeComparator $nodeComparator, IInsertStrategy $insertStrategy)
    {
        $this->nodeComparator = $nodeComparator;
        $this->insertStrategy = $insertStrategy;
    }
    
    /**
     * @param int $index
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function search($index)
    {
        $next = $this->getRoot();

        while (null !== $next && $index !== $next->getIndex()) {
            if ($index < $next->getIndex()) {
                $next = $next->getLeft();
            } else {
                $next = $next->getRight();
            }
        }

        if (null === $next) {
            throw new NoSuchElementException();
        }

        return $next;
    }

    /**
     * @return INode
     *
     * @throws EmptyTreeException
     */
    public function getMinimum()
    {
        $next = $this->getRoot();

        $minNode = $this->getMinNode($next);

        if (null === $minNode) {
            throw new EmptyTreeException();
        }
        
        return $minNode;
    }

    /**
     * @param INode|null $next
     *
     * @return mixed
     */
    private function getMinNode($next)
    {
        while (null !== $next && $next->hasLeft()) {
            $next = $next->getLeft();
        }

        return $next;
    }
    
    /**
     * @return INode
     *
     * @throws EmptyTreeException
     */
    public function getMaximum()
    {
        $next = $this->getRoot();

        $maxNode = $this->getMaxNode($next);
        
        if (null === $maxNode) {
            throw new EmptyTreeException();
        }
        
        return $maxNode;
    }
    
    /**
     * @param INode|null $next
     *
     * @return mixed
     */
    private function getMaxNode($next)
    {
        while (null !== $next && $next->hasRight()) {
            $next = $next->getRight();
        }

        return $next;
    }

    /**
     * @param INode $node
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function getPredecessor(INode $node)
    {
        if ($node->hasLeft()) {
            return $this->getMaxNode($node->getLeft());
        }
        
        $result = $this->get($node, function ($parent) {
            /**
             * @var INode $parent
             */
            return $parent->getLeft();
        });

        return $result;
    }

    /**
     * @param INode $node
     *
     * @return INode
     *
     * @throws NoSuchElementException
     */
    public function getSuccessor(INode $node)
    {
        if ($node->hasRight()) {
            return $this->getMinNode($node->getRight());
        }
        
        $result = $this->get($node, function ($parent) {
            /**
             * @var INode $parent
             */
            return $parent->getRight();
        });
        
        return $result;
    }

    /**
     * @param INode $node
     * @param $callback
     *
     * @return INode
     */
    private function get(INode $node, $callback)
    {
        /**
         * @var INode $parent
         */
        $parent = $node->getParent();

        while (null !== $parent && $node === $callback($parent)) {
            $node = $parent;

            $parent = $parent->getParent();
        }

        if (null === $parent) {
            throw new NoSuchElementException();
        }

        return $parent;
    }
    
    /**
     * @param INode $node
     */
    public function insert(INode $node)
    {
        $root = $this->getRoot();
        
        if (null === $root) {
            $this->root = $node;
        } else {
            $this->insertStrategy->insertNode($root, $node, $this->nodeComparator);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return null === $this->root;
    }

    /**
     * @param INode $node - удаляемы узел
     *
     * @return bool
     *
     * @throws EmptyTreeException
     */
    public function delete(INode $node)
    {
        if ($this->isEmpty()) {
            throw new EmptyTreeException();
        }
        
        if (false === $node->hasLeft() && false === $node->hasRight()) {
            $this->transplant($node, null);
        } elseif ($node->hasRight() && false === $node->hasLeft()) {
            $this->transplant($node, $node->getRight());
        } elseif ($node->hasLeft() && false === $node->hasRight()) {
            $this->transplant($node, $node->getLeft());
        } else {
            $parent = $node->getParent();
            $right = $node->getRight();
            $left = $node->getLeft();
            
            /**
             * Если у правого поддерева нет левого потомка,
             * то заменяем удаляемый узел($node) правым потомком, сохраняя левую ветвь удаляемого узла
             */
            if (false === $right->hasLeft()) {
                $right->setLeft($left);
                $left->setParent($right);
                
                if ($node->isRoot()) {
                    $right->setParent(null);
                    $this->root = $right;
                } else {
                    $parent->replaceChildNode($right);
                }
            } else {
                /**
                 * получаем минимальный Node из правого поддерева правого дерева удаляемого Node
                 * @var INode
                 */
                $minNode = $this->getMinNode($right);
                
                if ($minNode->getParent() !== $node) {
                    /**
                     * Заменяем минимальный элемент на его правое поддерево,
                     * в этом мести у минимального элемента соответствено нет левого подерева
                     */
                    $this->transplant($minNode, $minNode->getRight());
                    
                    /**
                     * устанавливаем min node на позицию удаляемого node
                     */
                    $minNode->setRight($node->getRight());
                    
                    /**
                     * заменяем парента у правого подерева удаляемой ноды на min node
                     */
                    $node->getRight()->setParent($minNode);
                }
                
                /**
                 * Заменяем node на min node
                 * (в этом случае в функции сработает else)
                 */
                $this->transplant($node, $minNode);

                /**
                 * Для левого поддерева node устанавливаем нового parent: $minNode
                 */
                $minNode->setLeft($node->getLeft());
                
                /**
                 * Перемещаем левое поддерево из удаляемого $node в $minNode как левое поддерево
                 */
                $minNode->getLeft()->setParent($left);
            }
        }

        $node->clear();
    }

    /**
     * @param INode $node
     * @param INode|null $replace
     */
    private function transplant($node, $replace)
    {
        $nodeParent = null !== $node ? $node->getParent() : null;
        
        if (false === $node->hasParent()) {
            $this->root = $replace;
        } elseif (null !== $nodeParent && $node === $nodeParent->getLeft()) {
            $nodeParent->setLeft($replace);
        } else {
            $nodeParent->setRight($replace);
        }
        
        /**
         * set new parent
         */
        if (null !== $replace) {
            $replace->setParent($node->getParent());
        }
    }
    
    /**
     * @return INode|null
     */
    private function getRoot()
    {
        return $this->root;
    }
}
