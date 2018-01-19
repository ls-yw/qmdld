<?php
namespace Basic;
use Phalcon\Db;
use Library\Log;


class BaseModel extends \Phalcon\Mvc\Model{
    
    public function reset(){}
    
    public function getSource(){
		$string = method_exists($this,'tableName')?$this->tableName():null;
		$table_name=$this->loadPrefix($string);
		return $table_name ? $table_name:parent::getSource();
	}
    
	public function initialize(){
		//初始化数据库
	    $this->setWriteConnectionService("dbMaster");
	    $this->setReadConnectionService("dbMaster");
	}
	
	/**
	 * 获取多条数据
	 * @param string $sql
	 * @param array $params
	 * @return array|bool
	 */
	public function getRows($sql, $params=[])
	{
        try{
    	    $rows = $this->readData($sql, $params);
	    } catch (\Exception $e) {
	        Log::write('sql', $e->getMessage(), '', 'error');
	        return false;
	    }
	    return $rows ? $rows : [];
	}

	/**
	 * 获取一条数据
	 * @param string $sql
	 * @param array $params
	 * @return mixed|string
	 */
	public function getRow($sql, $params=[])
	{
	    $row = $this->getRows($sql, $params);
	    
	    return $row ? $row[0] : '';
	}
	
	/**
	 * 获取单个字段
	 * @param str $sql
	 * @param array $params
	 * @param string $field 返回的字段
	 */
	public function getStr($sql, $params=[], $field = '')
	{
	    $row = $this->getRow($sql, $params);
	    if(!$row){
	        return false;
	    }
	    
	    if(!empty($field)){
	        if(isset($row[$field])){
	            return $row[$field];
	        }else{
	            return false;
	        }
	    }
	     
	    return current($row);
	}
	
	/**
	 * 执行sql(只写)
	 * @param string $sql
	 * @param array $params
	 * @return boolean|id     新增时返回新增的ID
	 */
	public function execute($sql, $params)
	{
        try {
            $rows = $this->getWriteConnection()->execute($sql,$params);
            
            return $this->getWriteConnection()->lastInsertId() > 0 ? $this->getWriteConnection()->lastInsertId() : true;
        }catch (\Exception $e){
            Log::write('sql', 'SQL:'.$sql.' VALUE:'.json_encode($params, JSON_UNESCAPED_UNICODE), '', 'error');
            Log::write('sql', $e->getMessage(), '', 'error');
            return false;
        }
	}
	
	/**
	 * 处理where条件，转化为sql
	 * array(
	 *     'id' => [1,2,3]               //id in (1,2,3)
	 *     'status' => ['in', [1,2]]     //status in (1,2)
	 *     'name' => ['like', '%产品%']   //name like '%产品%'
	 *     'year' => ['between', [2015,2017]] //year between 2015 AND 2017
	 *     'age' => ['>', 10]            //age > 10
	 *     'start_time' =>['or', 'start_time'=>['>=', '2017-11-17 17:20:10'], 'start_time'=>['<=', '2017-11-17 17:30:10']]  
	 *                                   //(start_time >= '2017-11-17 17:20:10') or (start_time <= '2017-11-17 17:30:10')
	 *     '_sql' => ['_sql', "(start_time >= '2017-11-17 17:20:10') or (start_time <= '2017-11-17 17:30:10')"],    //直接拼接后面的sql，若有多个 key 可随意写，但不能为数字
	 * )
	 * 默认 and 连接
	 * @param string|array $where
	 */
	public function dealWhere($where)
	{
	    if(empty($where))return ['where'=>'1=1', 'params'=>[]];
	    if(!is_array($where))return ['where'=>$where, 'params'=>[]];
	    
	    $fields = $val = [];
	    
        foreach ($where as $key => $value) {
            if(empty($key) || is_numeric($key))continue;
            if(is_string($value[0]))$value[0] = trim(strtolower($value[0]));
            if(is_array($value)){
                if(in_array($value[0], ['>', '>=', '<', '<=', 'like', '!=', '<>'])){
                    $fields[] = $key." {$value[0]} ?";
                    $val[] = $value[1];
                }elseif ($value[0] == 'between' && is_array($value[1])){
                    $fields[] = $key." between ? AND ?";
                    $val[] = $value[1][0];
                    $val[] = $value[1][1];
                }elseif ($value[0] == 'in'){
                    $w = [];
                    for($i=1;$i<=count($value[1]);$i++)  {
                        $w[] = '?';
                    }
                    $fields[] = $key." in (".implode(',', $w).')';
                    foreach ($value[1] as $v) {
                        $val[] = $v;
                    }
                }elseif ($value['0'] == 'or'){
                    $childWhere = [];
                    $childParams = [];
                    foreach ($value as $k => $v) {
                        if($k == 0)continue;
                        $tmp_Where = $this->dealWhere([$k=>$v]);
                        $childWhere[] = $tmp_Where['where'];
                        $childParams = empty($childParams) ? $tmp_Where['params'] : array_merge($childParams, $tmp_Where['params']);
                    }
                    $fields[] = ' ('.implode(') OR (', $childWhere).') ';
                    $val = array_merge($val, $childParams);
                }elseif ($value['0'] == 'or'){
                    $childWhere = [];
                    $childParams = [];
                    foreach ($value as $k => $v) {
                        if($k == 0)continue;
                        $tmp_Where = $this->dealWhere([$k=>$v]);
                        $childWhere[] = $tmp_Where['where'];
                        $childParams = empty($childParams) ? $tmp_Where['params'] : array_merge($childParams, $tmp_Where['params']);
                    }
                    $fields[] = ' ('.implode(') OR (', $childWhere).') ';
                    $val = array_merge($val, $childParams);
                }elseif ($value['0'] == '_sql'){
                    $fields[] = $value[1];
                }else{
                    $w = [];
                    for($i=1;$i<=count($value);$i++)  {
                        $w[] = '?';
                    }
                    $fields[] = $key." in (".implode(',', $w).')';
                    foreach ($value as $v) {
                        $val[] = $v;
                    }
                }
            }else{
                $fields[] = $key.' = ?';
                $val[] = $value;
            }
        }
        $whereSql = ' ('.implode(') AND (', $fields).') ';
        $paramSql = $val;
	    
        return ['where'=>$whereSql, 'params'=>$paramSql];
	}
	
	/**
	 * 表字段属性
	 * 
	 * @create_time 2017年11月17日
	 */
	public function attribute(){}
	
	/**
	 * 处理需要查询的字段
	 * @param string|array $fiedls
	 * @create_time 2017年11月17日
	 */
	public function dealFields($fiedls='')
	{
	    $defaultFields = $this->attribute();
	    if((empty($fiedls) || trim($fiedls) == '*') && !empty($defaultFields)){
	        $fiedls = array_keys($defaultFields);
	    }
	    if(is_array($fiedls))return '`'.implode('`,`', $fiedls).'`';
	    return $fiedls;
	}
	
	/**
	 * 处理新增数据
	 * @param int $data
	 */
	public function dealInsertData(array $data)
	{
	    $value = $fieldArr = $fieldPlaceholderArr = [];
	    foreach ($data as $key => $val) {
	        $fieldArr[]            = $key;
	        $fieldPlaceholderArr[] = '?';
	        $value[]               = $val;
	    }
	    $str = '(`'.implode('`,`', $fieldArr).'`) VALUES ('.implode(',', $fieldPlaceholderArr).')';
	    return ['val'=>$str, 'params'=>$value];
	}
	
	/**
	 * 处理更新数据
	 * @param array $data
	 * @create_time 2017年11月14日
	 */
	public function dealUpdateData(array $data)
	{
	    $value = $fieldArr = [];
	    foreach ($data as $key => $val) {
	        $field[]            = '`'.$key.'`=?';
	        $value[]            = $val;
	    }
	    
	    $str = implode(',', $field);
	    return ['val'=>$str, 'params'=>$value];
	}
	


	//原生SQL查询 (只读)
	# -----------------
	# 例:表全名sn_user 或设定前缀sn_, 可用 {{user}}
	# @params sql 必需
	# @cacheTime 类型:整数(单位秒,0 永久缓存)
	#
	# $this->readData("select * from sn_user|{{user}} where 1=1 limit 10"); 或
	# $this->readData("select * from sn_user|{{user}} where name=? limit 10",['小李']);
	# @return --\Simple 对像
	#
	#
	public function readData($sql=null,$bindParams=null,$cacheTime=null){
		if(!$sql || !is_string($sql)){
		    Log::write('sql', 'readData sql error：'.json_encode($sql), '', 'error');
			return [];
		}
		$sql=$this->loadPrefix($sql);

		//执行SQL
		try{
            $connection = $this->getReadConnection();
            $result = $connection->query($sql, $bindParams);
            $result->setFetchMode(Db::FETCH_ASSOC);
            $data = $result->fetchAll();

		}catch(\Exception $e){
		    Log::write('sql', 'SQL:'.$sql.' VALUE:'.json_encode($bindParams, JSON_UNESCAPED_UNICODE), '', 'error');
		    Log::write('sql', $e->getMessage(), '', 'error');
			return [];
		}
		return $data;
	}

	
	/**
	 * SQL重置前缀
	 * @param
	 * @return ***
	 */
	public function loadPrefix($string=null){
	    static $db_prefix=null;
	    if(!$db_prefix){
	        $dbConfig=$this->getReadConnection()->getDescriptor();
	        $db_prefix=isset($dbConfig['prefix'])?$dbConfig['prefix']:null;
	    }
	    if(!$string || !is_string($string)){
	        $preg_name=str_replace('\\', '_',get_class($this));
	        $string=preg_replace('/^(([^_]_?)*Models_?)/i', '',$preg_name );
	        $string='{{'.strtolower($string).'}}';
	    }
	
	    return preg_replace('/\{\{(.+?)\}\}/',$db_prefix.'\\1',$string);
	}

}


