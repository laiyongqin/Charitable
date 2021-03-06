<?php

/**
 * 管理员
 *
 * @author: moxiaobai
 * @since : 2016/1/29 10:36
 */

class BankModel extends BaseModel {
    public function __construct()
    {
        parent::__construct('charity', 't_bank', 'b_id');
    }


    /**
     * 获取列表
     *
     * @param array $where
     * @param array $pagination
     * @return bool
     */
    public function getList($where=array(), $pagination=array()) {
        $data = $this->_setWhereSQL($where)
                    ->_setPaginationSQL($pagination)
                    ->_db->select('*')
                    ->from($this->_table)
                    ->order($this->_primary_key, 'DESC')
                    ->fetchAll();

        if ( $data === FALSE ) return FALSE;
        return $data;
    }

    /**
     * 统计数量
     *
     * @param array $where
     */
    public function countData($where=array()) {
        return $this->_setWhereSQL($where)->_db->select('COUNT(*)')->from($this->_table)->fetchValue();
    }


    /**
     * @param $id
     */
    public function getInfo($id) {
        return $this->_db->select('*')->from($this->_table)->where($this->_primary_key, $id)->fetchOne();
    }


    /**
     * 保存数据
     *
     * @param array $data
     * @param int   $id
     * @return mixed
     */
    public function saveData($data, $id=0) {
        if($id > 0){
            return $this->_db->update($this->_table)->rows($data)->where($this->_primary_key, $id)->execute();
        }else{
            return $this->_db->insert($this->_table)->rows($data)->execute();
        }
    }


    /**
     * 改变状态
     *
     * @param $id
     * @param array $data 修改数据
     * @return mixed
     */
    public function changeData($id, $data) {
        return $this->_db->update($this->_table)->rows($data)->where($this->_primary_key, $id)->execute();
    }


    /**
     * SQL分页查询
     */
    private function _setPaginationSQL( $pagination = array() ) {
        if ( isset($pagination['page']) AND isset($pagination['pagesize']) ) {
            $page      = isset( $pagination['page'] ) ? intval($pagination['page']) : 1;
            $pagesize  = isset( $pagination['pagesize']  ) ? intval($pagination['pagesize'])  : 10;
            $this->_db->page($page, $pagesize);
        } elseif ( isset($pagination['limit']) ) {
            $this->_db->limit( intval($pagination['limit']) );
        }
        return $this;
    }

    /**
     * SQL Where条件
     * @param array $where
     * @return $this
     */
    private function _setWhereSQL ($where = array()) {
        return $this;
    }

    /**
     * 整合信息
     *
     * @param $data
     * @return mixed
     */
    public function mergData($data,$key='b_id') {
        if ( ! is_array( $data ) )
            return $data;

        $ids = Helper_Array::setIds($data, $key, TRUE);
        if ( ! $ids) {return $data;}

        //订单信息
        $info = $this->_db->select('b_id', 'b_name')->from($this->_table)->where("b_id IN({$ids})")->fetchAll();
        if ( $info === FALSE ) {return $data;}

        $info = Helper_Array::setIdsKey($info, 'b_id');

        foreach ( $data AS &$row ) {
            $row['b_id']   = isset($info[ $row[$key] ]) ? $info[ $row[$key] ]['b_id'] : '';
            $row['b_name']   = isset($info[ $row[$key] ]) ? $info[ $row[$key] ]['b_name'] : '';
        }
        return $data;
    }

}