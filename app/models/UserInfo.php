<?php
namespace Models;

use Basic\BaseModel;

class UserInfo extends BaseModel
{
    
    /**
     * 根据userID获取数据
     * @param int $id
     */
    public function getByUserId($userId) {
        $sql = "select * from user_info where user_id = ?";
        $params = [$userId];
        $info = $this->getRow($sql, $params);
    
        return $info;
    }
    
    /**
     * 新增数据
     * @param array $data
     * @return $row  新增数据的ID
     */
    public function insertData($data) {
        $data = $this->dealInsertData($data);
        $sql = "INSERT INTO user_info ".$data['val'];
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
        $sql = "UPDATE user_info set ".$data['val'].' where '.$whereSql['where'];
        $row = $this->execute($sql, $params);
        return $row;
    }
}