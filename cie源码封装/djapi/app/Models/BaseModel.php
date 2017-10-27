<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseModel extends Model
{
	/**
	 * 获取单条数据
	 * @param Array $where 查询条件
	 * @param string $table 表名
	 * @param Array $fields 查询字段
	 */
    public function getOne(Array $where,$table,Array $fields = array())
	{
		// 参数校验
		if (!$where) 	$where	= 1;
		if (!$fields) 	$fields = '*';
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}
		// 执行查询
		$json	= DB::table($table)->where($where)->select($fields)->first();
		$info	= $this->stdclassToArray($json);
		return $info;
	}

	/**
	 * 获取列表数据
	 * @param array  $where 查询条件
	 * @param string $table 表名
	 * @param Array $fields 查询字段
	 * @param string $count 每页显示条数
	 * @param array $orderby 排序方式
	 */
	public function getList(Array $where,$table,Array $fields = array(),$count = 0,Array $orderby = array('id','desc'))
	{// ['status','updated_at'],['desc','desc']
		// 参数校验
		if (!$where) 	$where	= 1;
		if (!$fields) 	$fields = '*';
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}
		// 执行查询
		if ($count) {
			$json = DB::table($table)->where($where)->select($fields)->orderBy($orderby[0],$orderby[1])->paginate($count);
		} else {
			$json = DB::table($table)->where($where)->select($fields)->orderBy($orderby[0],$orderby[1])->get();
		}
		$list	= $this->stdclassToArray($json);
		return $list;
	}

	/**
	 * 获取数据-whereIn查询条件
	 * @param array $where
	 * @param string $table
	 * @param array $whereIn
	 * @param bool $fields
	 */
	public function whereIn(Array $whereIn,$table,$fields = array(),$count = 0,$where = array())
	{
		// 校验参数
		if (!$fields) 	$fields = '*';
		if (!$whereIn) {
			writeLog('whereIn_empty:whereIn查询时，whereIn不能为空','table:'.$table);
			return false;
		}
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}
		// 执行查询
		if ($count) {
			$list = DB::table($table)
				->whereIn($whereIn[0],$whereIn[1])->where(function($query) use ($where) {
					if ($where) {
						$query->where($where);
					}
				})->select($fields)->paginate($count);
		} else {
			$list = DB::table($table)
				->whereIn($whereIn[0],$whereIn[1])->where(function($query) use ($where) {
					if ($where) {
						$query->where($where);
					}
				})->select($fields)->get();
		}

		$list	= $this->stdclassToArray($list);
		return $list;
	}


	/**
	 * left join联合查询
	 * @param Array $join_where
	 * @param Array $where
	 * @param string $tableA
	 * @param string $tableB
	 * @param Array $fields
	 * @param int $count 页面显示总条数
	 * @return ARRAY
	 */
	public function leftJoin(Array $join_where,Array $where, $tableA,$tableB,Array $fields,$count = 0)
	{
		// 校验参数
		if (!$join_where){
			writeLog('join_where_empty:join查询，join_where不能为空','tableA:'.$tableA.';tableB:'.$tableB);
			return false;
		}
		if (!$where) 	$where	= 1;
		if (!$fields) 	$fields = '*';
		if (!$tableA || !$tableB){
			writeLog('table_empty:表名不能为空','tableA:'.$tableA.';tableB:'.$tableB);
			return false;
		}

		// 执行查询
		if ($count) {
			$list = DB::table($tableA)
				->leftJoin($tableB,function($join) use ($join_where){
					$join->on($join_where);
				})
				->where($where)
				->select($fields)
				// ->orderBy($orderby[0],$orderby[1])
				->paginate($count);
		} else {
			$list = DB::table($tableA)
				->leftJoin($tableB,function($join) use ($join_where){
					$join->on($join_where);
				})
				->where($where)
				->select($fields)
				// ->orderBy($orderby[0],$orderby[1])
				->get();
		}

		return $this->stdclassToArray($list);
	}

	/**
	 * join联合查询
	 * @param Array $join_where
	 * @param Array $where
	 * @param string $tableA
	 * @param string $tableB
	 * @param Array $fields
	 * @param int $count 页面显示总条数
	 * @return ARRAY
	 */
	public function join(Array $join_where,Array $where, $tableA,$tableB,Array $fields,$count = 0)
	{
		// 校验参数
		if (!$join_where){
			writeLog('join_where_empty:join查询，join_where不能为空','tableA:'.$tableA.';tableB:'.$tableB);
			return false;
		}
		if (!$where) 	$where	= 1;
		if (!$tableA || !$tableB){
			writeLog('table_empty:表名不能为空','tableA:'.$tableA.';tableB:'.$tableB);
			return false;
		}
		if (!$fields) 	$fields = '*';

		// 执行查询
		$handle = DB::table($tableA)
			->join($tableB,function($join) use ($join_where){
				$join->on($join_where);
			})->where($where)
			->select($fields);
		if ($count) {
			$list	= $handle->paginate($count);
		} else {
			$list 	= $handle->get();
		}
		return $this->stdclassToArray($list);
	}

	/**
	 * 获取数据总条数
	 * @param array $where 查询条件
	 * @param string $table  表名
	 * @param string $key   count_key
	 */
	public function getCount(Array $where,$table,$key = 'id')
	{
		// 校验参数
		if (!$where) 	$where	= 1;
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}
		// 查询
		$json	= DB::table($table)->where($where)->count($key);
		$count	= $this->stdclassToArray($json);
		return $count;
	}

	/**
	 * 插入数据
	 * @param Array $data  数据
	 * @param string $table 表名
	 */
	public function insertData(Array $data,$table)
	{
		// 校验参数
		if (!$data) {
			writeLog('insert_data_empty:插入数据不能为空','table:'.$table);
			return false;
		}
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}

		// 插入
		$data['del']			= 0;
		$data['time']			= date('Y-m-d H:i:s',time());
		$data['update_time']	= date('Y-m-d H:i:s',time());
		$id = DB::table($table)->insertGetId($data);
		if (!$id) {
			writeLog('insert_fail:数据插入失败','table:'.$table,array('insert_data',$data));
			return false;
		}
		return $id;
	}

	/**
	 * 插入多条数据
	 * @param Array $data 插入数据
	 * @param $table 表名
	 */
	public function insertBatch(Array $multipleData,$table)
	{
		// 校验参数
		if (!$multipleData) {
			writeLog('insertBatch_data_empty:插入数据为空',$multipleData);
			return false;
		}
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}
		// 拼接插入数据
		foreach ($multipleData as $k=>&$v) {
			$v['del']			= 0;
			$v['time']			= date('Y-m-d H:i:s',time());
			$v['update_time']	= date('Y-m-d H:i:s',time());
		}
		// 执行插入操作
		$res = DB::table($table)->insert($multipleData);
		if (!$res) {
			writeLog('insertBatch_fail:数据插入失败','table:'.$table,array('insert_data',$multipleData));
			return false;
		}
		return true;
	}

	/**
	 * 更新数据
	 * @param array $where 更新条件
	 * @param string $data  更新数据
	 * @param string $table  表名
	 */
	public function updateData(Array $where,$data,$table)
	{
		// 校验参数
		if (!$where) $where = 1;
		if (!$table) {

		}
		if (!$table){
			writeLog('table_empty:表名不能为空','table:'.$table);
			return false;
		}

		// 执行更新
		$data['update_time']	= date('Y-m-d H:i:s',time());
		$res = DB::table($table)->where($where)->update($data);
		if (!$res) {
			writeLog('update_fail:数据更新失败','table:'.$table.'-where:'.json_encode($where),array('update_data',$data));
			return false;
		}
		return $res;
	}

	/**
	 * 更新多条数据
	 * @param Array $multipleData 更新数据
	 * @param $table 表名
	 */
	public function updateBatch(Array $multipleData,$table)
	{
		// 校验参数
		if (!$table) {
			writeLog('table_mepty:表为空',$multipleData);
			return false;
		}

		// 拼接更新参数
		foreach ($multipleData as $k=>&$v) {
			$v['update_time']	= date('Y-m-d H:i:s',time());
		}

		// 获取更新条件
		$updateColumn 	= array_keys($multipleData[0]);
		$key 			= $updateColumn[0]; //$key = id
		unset($updateColumn[0]);// array('name','age')

		// 拼接sql
		$whereIn = "";
		$q = "UPDATE dj_".$table." SET ";
		foreach ($updateColumn as $uColumn ) {
			$q .=  $uColumn." = CASE ";
			foreach( $multipleData as $data ) {
				$q .= "WHEN ".$key." = ".$data[$key]." THEN '".$data[$uColumn]."' ";
			}
			$q .= "ELSE ".$uColumn." END, ";
		}
		foreach( $multipleData as $data ) {
			$whereIn .= "'".$data[$key]."', ";
		}
		$q = rtrim($q, ", ")." WHERE ".$key." IN (".  rtrim($whereIn, ', ').")";

		// 执行查询
		$res	= DB::update(DB::raw($q));
		if (!$res) {
			writeLog('updateBatch_fail:数据批量更新失败',$q,array('update_data',$multipleData));
			return false;
		}
		return true;
	}

	/**
	 * 执行原始sql查询语句[防sql注入]
	 * @param string $sql SQL语句
	 * @param Array $params SQL参数
	 */
	public function getSQL($sql,Array $params)
	{
		// 参数校验
		if (!$sql) {
			writeLog('sql_empty:sql不能为空','sql:'.$sql);
			return false;
		}
		// 执行sql查询
		$json	= DB::select($sql, $params);
		$result	= $this->stdclassToArray($json);
		return $result;
	}

	/**
	 * stdclass转换成array类型
	 * @param stcclass $stdclass
	 */
	public function stdclassToArray($stdclass)
	{
		return json_decode(json_encode($stdclass),true);
	}
}
