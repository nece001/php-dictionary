<?php

namespace Nece001\PhpDictionary\ThinkPHP;

use Nece001\PhpDictionary\ThinkPHP\Model\DictionaryItem;

class DictionaryItemLogic
{
    protected $model;


    public function createModel()
    {
        return new DictionaryItem();
    }


    protected function getModel()
    {
        if (!$this->model) {
            $this->model = $this->createModel();
        }
        return $this->model;
    }



    public function saveDictionary($title, $key_name = '')
    {
    }
}
