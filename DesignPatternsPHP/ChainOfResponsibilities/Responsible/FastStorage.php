<?php

namespace MyLib\DesignPatterns\ChainOfResponsibilities\Responsible;

use DesignPatterns\ChainOfResponsibilities\Handler;
use DesignPatterns\ChainOfResponsibilities\Request;

/**
 * Class FastStorage
 */
class FastStorage extends Handler
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    protected function processing(Request $req)
    {
        if ('get' === $req->verb) {
            if (array_key_exists($req->key, $this->data)) {
                // the handler IS responsible and then processes the request
                $req->response = $this->data[$req->key];
                // instead of returning true, I could return the value but it proves
                // to be a bad idea. What if the value IS "false" ?
                return true;
            }
        }

        return false;
    }
}
