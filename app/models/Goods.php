<?php
namespace Models;

use Basic\BaseModel;

class Goods extends BaseModel
{
    
    /**
     * 根据userID获取数据
     * @param int $id
     */
    public function getById($id) {
        $sql = "select * from goods where id = ?";
        $params = [$id];
        $info = $this->getRow($sql, $params);
    
        return $info;
    }
    
    /**
     * 获取数据列表
     * @param string|array $where
     * @param string $orderBy
     * @param number $offset
     * @param number $row
     */
    public function getList($where, $orderBy='id desc', $offset=0, $row=20) {
        $whereSql = $this->dealWhere($where);
    
        if(!empty($orderBy))$orderBy = 'order by '.$orderBy;
    
        $sql = "select * from goods where ".$whereSql['where']." {$orderBy}";
        if($offset !== false)$sql .= " limit {$offset},{$row}";
    
        $list = $this->getRows($sql, $whereSql['params']);
        return $list;
    }
    
    /**
     * 新增数据
     * @param array $data
     * @return $row  新增数据的ID
     */
    public function insertData($data) {
        $data = $this->dealInsertData($data);
        $sql = "INSERT INTO goods ".$data['val'];
        $row = $this->execute($sql, $data['params']);
        return $row;
    }
    
    /**
     * 更新数据
     * @param array $data   更新的数据
     * @param array $where  更新条件
     * @create_time 2017年11月14日
     */
    public function updateData($data, $where) {
        $data = $this->dealUpdateData($data);
        $whereSql = $this->dealWhere($where);
        $params = array_merge($data['params'], $whereSql['params']);
        $sql = "UPDATE goods set ".$data['val'].' where '.$whereSql['where'];
        $row = $this->execute($sql, $params);
        return $row;
    }
}