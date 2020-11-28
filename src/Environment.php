<?php
namespace duihao;

//环境信息类
class Environment
{
    /**
     * 构造函数，解析数组获取环境信息
     * @param array $environment
     */
    public function __construct($environment = null)
    { 
        if (!is_null($environment)) {
            $this->parse($environment);
        }
    }

    /**
     * 解析环境信息数组
     *
     * This method will parse an environment array and add the data to
     * this collection
     *
     * @param  array $environment
     * @return void
     */
    public function parse(array $environment)
    {
        foreach ($environment as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * set value 设置值
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * get  获取值
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->$key;
    }
}
