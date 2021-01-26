<?php

namespace App\HttpController\Admin;



use App\Model\CategoryModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Category extends \App\HttpController\Admin\Base
{
    /**
     * 栏目列表
     */
    public function lists(){
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $model = new CategoryModel();
        if(!empty($this->param['name'])){ $model->where('name like "%'.$this->param['name'].'%"'); }
        $field = "*";
        $list = $model->withTotalCount()
            ->order('sort,id', 'ASC')->field($field)->limit($limit * ($page - 1), $limit)->all();
        $total = $model->lastQueryResult()->getTotalCount();;
        $this->writeJson(Status::CODE_OK, ['total'=>$total,'list'=>$list], 'success'.$model->lastQuery()->getLastPrepareQuery());
        return true;
    }
    /**
     * 新增微信栏目
     */
    public function add(){
        $data['name'] = $this->param['name'];
        $data['image'] = $this->param['image'];
        $data['url_name'] = $this->param['url_name']??'';
        $data['describe'] =  $this->param['describe']??'';
        $data['desc'] =  $this->param['describe']??'';
        $data['sort'] =  $this->param['sort']??1;
        $data['price'] = $this->param['price']??0;
        $data['vip_price'] = $this->param['vip_price']??0;
        $data['line_price'] = $this->param['line_price']??0;
        $data['remark'] =  $this->param['remark']??'';
        $data['status'] = $this->param['status']??0;
        $data['is_hot'] = $this->param['is_hot']??0;
        $data['is_reset'] = $this->param['is_reset']??0;
        $data['create_time'] = time();
        $data['update_time'] = time();
//        if(empty($data['url_name'])){$this->writeJson(Status::CODE_BAD_REQUEST, $this->param, '页面模板必须');return false; }
        if(empty($data['name'])){$this->writeJson(Status::CODE_BAD_REQUEST, $this->param, '栏目名称必须');return false; }
        if(empty($data['price'])){$this->writeJson(Status::CODE_BAD_REQUEST, $this->param, '价格必须');return false; }

        if(CategoryModel::create()->where('name',$data['name'])->get()){
            $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0], '栏目名称已存在');return false;
        }

        try{
            if($wechat_id = CategoryModel::create()->data($data)->save()){
                $object = CategoryModel::create()->where('status',1)->order('sort','asc')->select();
                Cache::getInstance()->set('object',$object);
                $this->writeJson(Status::CODE_OK, ['status'=>1], '新增栏目成功');return false;
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0], '新增栏目失败');return false;
            }
        }catch (\Exception $e){
            $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0], '插入数据库错误：'.$e->getMessage());return false;
        }

        return false;
    }

    /**
     * 更新栏目
     */
    public function edit(){

        if(!empty($this->param['id'])){
            $data['name'] = $this->param['name'];
            $data['price'] = $this->param['price'];
            $data['vip_price'] = $this->param['vip_price']??0;
            $data['line_price'] = $this->param['line_price']??0;
            $data['remark'] =  $this->param['remark']??'';
            $data['image'] = $this->param['image'];
//            $data['url_name'] = $this->param['url_name'];
            $data['describe'] =  $this->param['describe']??'';
            $data['desc'] =  $this->param['describe']??'';
            $data['sort'] =  $this->param['sort']??1;
            $data['is_hot'] = $this->param['is_hot']??0;
            $data['is_reset'] = $this->param['is_reset']??0;
            $data['status'] =  $this->param['status']??0;
            $data['update_time'] = time();
            if(empty($data['name'])){$this->writeJson(Status::CODE_BAD_REQUEST, $this->param, '栏目名称必须');return false; }
            if(empty($data['price'])){$this->writeJson(Status::CODE_BAD_REQUEST, $this->param, '价格必须');return false; }
            if(CategoryModel::create()->where('name',$data['name'])->where('id',$this->param['id'],'<>')->get()){
                $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0], '栏目名称已存在');return false;
            }


            try{
                if(CategoryModel::create()->update($data,['id'=>$this->param['id']])){
                    $object = CategoryModel::create()->where('status',1)->order('sort','asc')->select();
                    Cache::getInstance()->set('object',$object);
                    $this->writeJson(Status::CODE_OK, ['status'=>1], '更新栏目成功');return false;
                }else{
                    $this->writeJson(Status::CODE_OK, ['status'=>0], '更新栏目失败');return false;
                }
            }catch (\Exception $e){
                $this->writeJson(Status::CODE_OK, ['status'=>0], '更新出错：'.$e->getMessage());
            }

        }else{
            $this->writeJson(Status::CODE_OK, ['status'=>0], '栏目ID不存在');
        }
        return false;
    }

    /**
     * 更新栏目状态
     * param id
     * param status
     * return bool
     */
    public function doStatus(){
        $id = $this->param['id'];
        if(empty($this->param['field'])){ $this->writeJson(Status::CODE_OK,  ['status'=>0], '要更新的字段必须'); return false;}
        if(empty($id)){ $this->writeJson(Status::CODE_OK,  ['status'=>0], '栏目ID必须'); return false;}
        $value = (int)$this->param['value']??0;

        if(CategoryModel::create()->update([$this->param['field']=>$value,'update_time'=>time()],['id'=>$id])){
            $this->writeJson(Status::CODE_OK,  ['status'=>1], '操作成功');
        }else{
            $this->writeJson(Status::CODE_OK,  ['status'=>0], '操作失败');
        }
        return true;
    }

    /**
     * 上传图片
     * @return bool
     */
    public function upload(){
        $request=  $this->request();
        $img_file = $request->getUploadedFile('file');//获取一个上传文件,返回的是一个\EasySwoole\Http\Message\UploadFile的对象
        $fileSize = $img_file->getSize();
        //上传图片不能大于5M (1048576*5)
        if($fileSize>1048576){
            $this->writeJson(Status::CODE_BAD_REQUEST,['size'=>$fileSize], '文件最大不能超过1MB'); return false;
        }
        $clientFileName = $img_file->getClientFilename();
        $fileName = '_'.MD5(time()).'.'.pathinfo($clientFileName, PATHINFO_EXTENSION);;
        $res = $img_file->moveTo(EASYSWOOLE_ROOT.'/public/uploads/category/'.$fileName);
        if($res===true){
            $data['path'] = '/public/uploads/category/'.$fileName;
            $this->writeJson(Status::CODE_OK, $data, 'success');
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST,EASYSWOOLE_ROOT.'/public/uploads/category/'.$fileName, '文件上传失败');
        }
        return true;
    }

    /**
     * 删除品牌
     */
    public function del(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            if( CategoryModel::create()->where('id',$ids,'in')->destroy()){
                $object = CategoryModel::create()->where('status',1)->order('sort','asc')->select();
                Cache::getInstance()->set('object',$object);
                $this->writeJson(Status::CODE_OK, ['status'=>1], '删除栏目成功');return false;
            }else{
                $this->writeJson(Status::CODE_OK, ['status'=>0], '删除栏目失败');return false;
            }
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0,'param'=>$this->param], '请选择要删除的栏目');
        }
        return false;
    }

    /**
     * 获取全部栏目
     */
    public function all(){
        $model = CategoryModel::create();
        $field = "id as value,name";
        $order = ['id','desc'];
        $list = $model->order($order[0], $order[1])->field($field)->limit(0,20)->all();
        $this->AjaxJson(1, $list, 'success');
        return true;
    }

}

