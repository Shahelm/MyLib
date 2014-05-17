<?php

class HistoryLastVisitedProducts 
{
    /**
     * @var HistoryLastVisitedProducts
     */
    private static $instance;
    
    /**
     * @var HistoryStorage
     */
    private $historyStorage;

    /**
     * @return self
     */
    public static function getInstance()
    {

        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * This function adds the product to the history of recently visited products. 
     * 
     * @param int $productId
     * @param string $url
     * @param bool $isSuperProduct
     * 
     * @return bool
     */
    public function addProductToHistory($productId, $url, $isSuperProduct)
    {
        return $this->historyStorage->insert($productId, $url, $isSuperProduct);
    }

    /**
     * The function returns an array of products that the user last visited.
     * 
     * @return array|bool(false) 
     */
    public function getProducts()
    {
        
    }

    private function __construct()
    {
        $this->historyStorage = FactoryHistoryStorage::getHistoryStorage();
    }

    private function __clone() { }

    private function __wakeup() { }
}
