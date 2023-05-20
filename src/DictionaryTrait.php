<?php

namespace Nece001\PhpDictionary;

trait DictionaryTrait
{

    protected $field_id = 'id';
    protected $field_parent_id = 'parent_id';
    protected $field_value = 'value';
    protected $field_value_type = 'value_type';
    protected $field_key_name = 'key_name';
    protected $field_title = 'title';

    /**
     * 获取所有数据列表
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @return \Iterator
     */
    abstract protected function allList();

    /**
     * 获取键名对应的值
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @param string $key_name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getValue($key_name, $default = null)
    {
        $data = $this->getAllData();
        return isset($data['kv'][$key_name]) ? $data['kv'][$key_name] : $default;
    }

    /**
     * 获取键名对应的子项
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @param string $key_names
     *
     * @return array
     */
    public function getOptions($key_names)
    {
        $result = array();
        $data = $this->getAllData();

        if (is_array($key_names) && count($key_names) > 1) {
            foreach ($key_names as $key_name) {
                if (isset($data['key_name_index'][$key_name])) {
                    $index = $data['key_name_index'][$key_name];
                    $result[$key_name][] = $data['dict'][$index];
                }
            }
        } else {
            if (isset($data['key_name_index'][$key_names])) {
                $index = $data['key_name_index'][$key_names];
                $result = $data['dict'][$index];
            }
        }
        return $result;
    }

    /**
     * 获取所有数据
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @return array
     */
    public function getAllData()
    {
        static $data = null;
        if (is_null($data)) {
            $list = $this->allList();
            $data = $this->buildDataByList($list);
        }

        return $data;
    }

    /**
     * 把数据列表组织成数组结构化数据
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @param \ArrayAccess $list
     *
     * @return array
     */
    protected function buildDataByList($list)
    {
        $kv = array();
        $address = array();
        $key_name_index = array();
        $dict = array(0 => array('childs' => array(), 'key_name' => 'root', 'title' => ''));
        $address[0] = &$dict[0];
        foreach ($list as $row) {

            $id = $row[$this->field_id];
            $parent_id = $row[$this->field_parent_id];
            $title = $row[$this->field_title];
            $key_name = $row[$this->field_key_name];
            $value = $this->fetchValue($row);

            if (!isset($dict[$id])) {
                $dict[$id] = array('childes' => array(), 'key_name' => $key_name, 'title' => $title);
                $address[$id] = &$dict[$id];
            }

            if (!isset($dict[$parent_id])) {
                $dict[$parent_id] = array('childes' => array(), 'key_name' => '', 'title' => '');
                $address[$parent_id] = &$dict[$parent_id];
            }

            if ($key_name) {
                $kv[$key_name] = $value;
                $key_name_index[$key_name] = $id;
            }

            $address[$id]['title'] = $title;
            $address[$id]['key_name'] = $key_name;
            $address[$parent_id]['childs'][] = array(
                'value' => $value,
                'title' => $title
            );
        }

        return array('dict' => $dict, 'kv' => $kv, 'key_name_index' => $key_name_index);
    }

    /**
     * 获取值
     *
     * @Author gjw
     * @DateTime 2023-05-20
     *
     * @param \ArrayAccess $row
     *
     * @return float|string
     */
    protected function fetchValue($row)
    {
        $type = $row[$this->field_value_type];
        $value = $row[$this->field_value] ? $row[$this->field_value] : $row[$this->field_id];
        if ($type == 'number') {
            return floatval($value);
        }

        return $value;
    }
}
