<?php
//模型类，加载了外部的数据库驱动类和缓存类
class Model{
    public $db = NULL; // 当前数据库操作对象
	public $cache = NULL;	//缓存对象
	public $sql = '';	//sql语句，主要用于输出构造成的sql语句
	public  $pre = '';	//表前缀，主要用于在其他地方获取表前缀
	public $config =array(); //配置
    protected $options = array(); // 查询表达式参数	
	
    public function __construct( $config = array() ) {
		$this->config = array_merge(Config::get('DB'), $config);	//参数配置	
		$this->options['field'] = '*';	//默认查询字段
		$this->pre = $this->config['DB_PREFIX'];	//数据表前缀
		$this->connect();
    }
	
	//连接数据库
	public function connect() {
		$dbDriver = ucfirst( $this->config['DB_TYPE'] );
		require_once( dirname(__FILE__) . '/db/' . $dbDriver . '.class.php' );
		$this->db = new $dbDriver( $this->config );	//实例化数据库驱动类	  
	}
	
	//设置表，$$ignore_prefix为true的时候，不加上默认的表前缀
	public function table($table,$simple=null, $ignorePre = false) {
		if ( $ignorePre ) {
			$this->options['table'] = $table.' '.$simple;
		} else {
			$this->options['table'] = $this->config['DB_PREFIX'] . $table.' '.$simple;;
		}
		return $this;
	}
	
	 //回调方法，连贯操作的实现
    public function __call($method, $args) {
		$method = strtolower($method);
        if ( in_array($method, array('field','data','where','group','having','order','limit','cache')) ) {
            $this->options[$method] = $args[0];	//接收数据
			if( $this->options['field'] =='' ) $this->options['field'] = '*'; 
			return $this;	//返回对象，连贯查询
        } else{
			throw new Exception($method . '方法在Model.class.php类中没有定义');
		}
    }
	
	//执行原生sql语句，如果sql是查询语句，返回二维数组
    public function query($sql, $params = array(), $is_query = false) {
        if ( empty($sql) ) return false;
		$sql = str_replace('{pre}', $this->pre, $sql);	//表前缀替换
		$this->sql = $sql;
		//判断当前的sql是否是查询语句
		if ( $is_query || stripos(trim($sql), 'select') === 0 ) {
			$data = $this->_readCache();
			if ( !empty($data) ) return $data;

			$query = $this->db->query($this->sql, $params);		
			while($row = $this->db->fetchArray($query)) {
				$data[] = $row;
			}
			$this->_writeCache($data);
			return $data;				
		} else {
			return $this->db->execute($this->sql, $params); //不是查询条件，直接执行
		}
    }
	//执行原生sql语句，如果sql是查询语句，返回二维数组
    public function sql_query($sql) {
        if ( empty($sql) ) return false;
		return mysql_fetch_array($this->query($sql));
    }	
	//统计行数
	public function count() {
		$table = $this->options['table'];	//当前表
		$field = 'count(*)';//查询的字段
		$where = $this->_parseCondition();	//条件
		$table_array = $this->options['add_table'];//多表
		if(is_array($table_array)){
			foreach ($table_array as $value) {
				$table_add.=$value['table'];
			}
		}
		$this->sql = "SELECT $field FROM $table $table_add $where";	//这不是真正执行的sql，仅作缓存的key使用
		
		$data = $this->_readCache();
		if ( !empty($data) ) return $data;
		
		$data = $this->db->count($table,$table_add, $where);
		$this->_writeCache($data);
		$this->sql = $this->db->sql; //从驱动层返回真正的sql语句，供调试使用
		return $data;
	}
	
	//只查询一条信息，返回一维数组	
    public function find() {
		$this->options['limit'] = 1;	//限制只查询一条数据
		$data = $this->select();
		return isset($data[0]) ? $data[0] : false;
     }
	 
	//查询多条信息，返回数组
     public function select() {
		$table = $this->options['table'];	//当前表
		$field = $this->options['field'];	//查询的字段
		$where = $this->_parseCondition();	//条件
		$table_array = $this->options['add_table'];//多表
		if(is_array($table_array)){
			foreach ($table_array as $value) {
				$table_add.=$value['table'];
			}
		}
		return $this->query("SELECT $field FROM $table $table_add $where", array(), true);
     }
	 
	 //获取一张表的所有字段
	 public function getFields() {
		$table = $this->options['table'];
		$this->sql = "SHOW FULL FIELDS FROM {$table}"; //这不是真正执行的sql，仅作缓存的key使用
	
		$data = $this->_readCache();
		if ( !empty($data) ) return $data;
		
		$data = $this->db->getFields( $table );
		$this->_writeCache( $data );
		$this->sql = $this->db->sql; //从驱动层返回真正的sql语句，供调试使用
		return $data;
	}
	
	 //插入数据
    public function insert( $replace = false ) {
		$table = $this->options['table'];	//当前表
		$this->format_data_by_fill($this->options['data']); //过滤多余字段
		$data = $this->_parseData('add');	//要插入的数据
		$INSERT = $replace ? 'REPLACE' : 'INSERT';
        $this->sql = "$INSERT INTO $table $data" ;
        $query = $this->db->execute($this->sql);
		if ( $this->db->affectedRows() ) {
			 $id = $this->db->lastId();
			 return empty($id) ? $this->db->affectedRows() : $id;
		}
        return false;
    }
	
	//替换数据
	 public function replace() {
		return $this->insert( true );
    }
	
	//修改更新
    public function update() {
		$table = $this->options['table'];	//当前表
		$this->format_data_by_fill($this->options['data']); //过滤多余字段
		$data = $this->_parseData('save');	//要更新的数据
		$where = $this->_parseCondition();	//更新条件
		if ( empty($where) ) return false; //修改条件为空时，则返回false，避免不小心将整个表数据修改了
			
        $this->sql = "UPDATE $table SET $data $where" ;
	    $query = $this->db->execute($this->sql);
		return $this->db->affectedRows();
    }
	
	//删除
    public function delete() {
		$table = $this->options['table'];	//当前表
		$where = $this->_parseCondition();	//条件
		if ( empty($where) ) return false; //删除条件为空时，则返回false，避免数据不小心被全部删除
			
		$this->sql = "DELETE FROM $table $where";
        $query = $this->db->execute($this->sql);
		return $this->db->affectedRows();
    }
	
	//数据过滤
	public function escape($value) {
		return $this->db->escape($value); 
	}
	
	//返回sql语句
    public function getSql() {
        return $this->sql;
    }

	//删除数据库缓存
    public function clear() {
		if ( $this->initCache() ) {
			return $this->cache->clear();
		}
		return false;
    }
	
	 //初始化缓存类，如果开启缓存，则加载缓存类并实例化
	public function initCache() {		
		if (is_object($this->cache)) {
			return true;
		} else if ($this->config['DB_CACHE_ON']) {
			require_once( dirname(__FILE__) . '/Cache.class.php' );
			$this->cache = new Cache($this->config, $this->config['DB_CACHE_TYPE']);
			return true;
		} else {
			return false;
		}
	}
	
	//读取缓存
	private  function _readCache() {
		$this->clear_where();
		isset($this->options['cache']) or $this->options['cache'] = $this->config['DB_CACHE_TIME'];
		//缓存时间为0，不读取缓存
		if ($this->options['cache'] == 0)
			return false;
		if ($this->initCache()) {
			$data = $this->cache->get($this->sql);
			if ( !empty($data) ) {
				unset($this->options['cache']);
				return $data;
			}
		}
		return false;
	}
	
	//写入缓存
	private function _writeCache($data) {
		$this->clear_where();
		//缓存时间为0，不设置缓存
		if ( $this->options['cache'] == 0)
			return false;		
		if ( $this->initCache() ) {				
			$expire = $this->options['cache'];
			unset($this->options['cache']);
			return $this->cache->set($this->sql, $data, $expire);	
		}
		return false;	
	}
	
	//解析数据  
	private function _parseData($type) {
		$data = $this->db->parseData($this->options, $type);
		$this->options['data'] = '';
		return $data;
	}
	
	//解析条件
	private function _parseCondition() {
		$condition = $this->db->parseCondition($this->options);
		$this->options['where'] = '';
		$this->options['group'] = '';
		$this->options['having'] = '';
		$this->options['order'] = '';
		$this->options['limit'] = '';
		$this->options['field'] = '*';		
		return $condition;		
	}

//==================================添加内容========================================
	//格式化字段
    public function format_data_by_fill(array $data=array()){
        $defaultData=$this->fields_default();
        $array=array();
        if(empty($data)) return $array;
        foreach ($defaultData as $key => $value) {
        	if(isset($data[$key])){
        		$array[$key]=$data[$key];
        	}
        }
        $this->options['data']=$this->quote_array($array); 
    }

	//获取一张表的字段名
	public function fields_default() {
		$data=$this->getFields();
		foreach ($data as $field) {
			$fields_default[$field['Field']]=$field['Default'];
		}
		return $fields_default;
	}

	//格式化字段类型
	public function quote_array(&$valueArr){
        return array_map(array(&$this,'quote'), $valueArr);
    }

    public function quote($value) {
        if (is_null($value)) return 'NULL';
        if (is_bool($value)) return $value ? 1 : 0;
        if (is_int($value)) return (int) $value;
        if (is_float($value)) return (float) $value;
        if (@get_magic_quotes_gpc())  $value = @stripslashes($value);
        return $value;
    }

    //添加表
	public function add_table($table,$simple=null,$where=null, $relation='AND' ,$ignorePre = false) {
		if ($ignorePre) {
			 $this->options['add_table'][$simple]['table'] = ','.$table.' '.$simple;
		} else {
			$this->options['add_table'][$simple]['table'] = ','.$this->config['DB_PREFIX'] . $table.' '.$simple;
		}
		$this->options['add_table'][$simple]['where'] =' '.$relation.' '.$where;
		return $this;
	}

	//清除查询条件
    public function clear_where() {
        $this->options['add_table']=null;
    }


}