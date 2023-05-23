<?php

namespace Nece001\PhpDictionary\ThinkPHP;

use Exception;
use Nece001\PhpCategoryTree\CategoryAbstract;
use Nece001\PhpDictionary\ThinkPHP\Model\Dictionary;

/**
 * 字典分组
 *
 * @Author gjw
 * @DateTime 2023-05-23
 */
class DictionaryLogic extends CategoryAbstract
{
    /**
     * 创建Model
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @return \Nece001\PhpOrmModel\Interfaces\Model
     */
    public function createModel()
    {
        return new Dictionary();
    }

    /**
     * 保存记录（有id则更新）
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @param string $title
     * @param string $key_name
     * @param integer $id
     *
     * @return \Nece001\PhpOrmModel\Interfaces\Model
     */
    public function save($parent_id, $title, $key_name, $is_disabled, $id = 0)
    {
        if ($this->exists($key_name, $id)) {
            throw $this->createException('key_name_exists');
        } else {
            if ($id) {
                $item = $this->getById($id);
            } else {
                $item = $this->createModel();
                $item->parent_id = $parent_id;
            }

            if ($item) {
                $item->title = $title;
                $item->key_name = $key_name;
                $item->is_disabled = $this->disabledState($is_disabled);

                if ($id) {
                    $this->saveUpdateItem($item, $parent_id);
                } else {
                    $this->saveCreateItem($item);
                }
            }

            return $item;
        }
    }

    /**
     * 删除
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @param array $ids
     *
     * @return int
     */
    public function delete($ids)
    {
        return $this->getModel()->whereIn('id', $ids)->delete();
    }

    /**
     * 键名是否存在
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @param string $key_name
     * @param integer $id
     *
     * @return bool
     */
    protected function exists($key_name, $id = 0)
    {
        $query = $this->getModel()->where('key_name', $key_name);
        if ($id) {
            $query->where('id', '<>', $id);
        }

        return $query->count() > 0;
    }

    /**
     * 创建异常
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @param string $message
     *
     * @return Exception
     */
    protected function createException($message)
    {
        return new Exception($message);
    }

    /**
     * 获取禁用状态
     *
     * @Author gjw
     * @DateTime 2023-05-23
     *
     * @param boolean $yes
     *
     * @return int
     */
    protected function disabledState($yes = true)
    {
        return $yes ? 1 : 0;
    }

    public function getValue($key_name)
    {
        $query = $this->getModel()->alias('a')->where('key_name', $key_name);

        return $query->find();
    }
}
