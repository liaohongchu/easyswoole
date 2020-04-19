<?php
namespace App\Model;

use EasySwoole\ORM\AbstractModel;

class GoodsModel extends AbstractModel
{
    protected $tableName = 'tp_goods';

    protected $primaryKey = 'goods_id';

    /**
     * @getAll
     * @keyword adminName
     * @param  int  page  1
     * @param  string  keyword
     * @param  int  pageSize  10
     * @return array[total,list]
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) 
        {
            $where['goods_name'] = ['%' . $keyword . '%', 'like'];
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    public function login():?GoodsModel
    {
        //$info = $this->get(['adminAccount' => $this->adminAccount, 'adminPassword' => $this->adminPassword]);
        $info = [];
        return $info;
    }

    /*
     * 以account进行查询
     */
    public function accountExist($field = '*'):?GoodsModel
    {
        $info = $this->field($field)->get(['adminAccount' => $this->adminAccount]);
        return $info;
    }

    public function getOneBySession($field = '*'):?GoodsModel
    {
        $info = $this->field($field)->get(['adminSession' => $this->adminSession]);
        return $info;
    }

    public function logout()
    {
        return $this->update(['adminSession' => '']);
    }

}