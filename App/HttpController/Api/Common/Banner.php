<?php

namespace App\HttpController\Api\Common;

use App\Model\GoodsModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Validate\Validate;

/**
 * Class Banner
 * Create With Automatic Generator
 */
class Banner extends CommonBase
{

    /**
     * getOne
     * @Param(name="goodsid", alias="主键id", required="", integer="")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author Tioncico
     * Time: 14:03
     */
    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new GoodsModel();
        $model->goods_id = $param['goodsid'];

        //print_r($model->goods_id); 

        $bean  = $model->get( array('goods_id'=>$model->goods_id) );
        if ($bean) {
            $this->writeJson(Status::CODE_OK, $bean, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }

    /**
     * getAll
     * @Param(name="page", alias="页数", optional="", integer="")
     * @Param(name="limit", alias="每页总数", optional="", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="32")
     * @author Tioncico
     * Time: 14:02
     */
    public function getAll()
    {
        $param = $this->request()->getRequestParam();
        $page  = $param['page'] ?? 1;
        $limit = $param['limit'] ?? 20;
        $model = new GoodsModel();
        $data  = $model->getAll($page, $param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }
}