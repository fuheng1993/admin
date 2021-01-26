<?php

namespace App\HttpController\Admin;



use App\Model\TrackerPoint\TrackerPointModel;
use App\Model\WechatModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Tracker extends \App\HttpController\Admin\Base
{
    /**
     * 获取链路记录列表
     */

    public function lists(){
        $model = TrackerPointModel::create();
        $param = $this->request()->getRequestParam();
        if(!empty($param['uri']) ){$model->where('uri',"%{$param['uri']}%",'like');}
        if(!empty($param['result']) ){$model->where('data',"%{$param['result']}%",'like');}
        if(!empty($param['ip']) ){$model->where('ip',"%{$param['ip']}%",'like');}
        if(!empty($this->param['start'])){ $model->where('startTime',strtotime($this->param['start']),'>='); }
        if(!empty($this->param['end'])){ $model->where('startTime',strtotime($this->param['end']),'<='); }
        if(!empty($this->param['spend'])){ $model->where('spendTime',$this->param['spend'],'>='); }
        $limit =$param['limit']??10;
        $p =$param['page']??1;
        $list = $model->limit($limit * ($p - 1), $limit)->field('uri,create_date,ip,status,spendTime,result,data as return_data')->order('id','desc')->select();
        ///$total = $model->lastQueryResult()->getTotalCount();
        $total = 2000;
        if(is_object($list)){$list = $list->toArray();}
        foreach ($list as $k=>$v){
//            $exp = explode($this->request()->getHeaders()['host'][0],$v['uri']);
//            $v['uri'] = $exp[1]??'';
//            $uri = explode(':9501/',$v['uri']);
//            if(count($uri)!=2){
//                $uri = explode(':80/',$v['uri']);
//            }
//            $list[$k]['uri'] = !empty($uri[1])?$uri[1]:$v['uri'];
            $list[$k]['uri'] = $v['uri'];
            $result = json_decode($v['result'],true);
            unset($result['startArg']['uri']);
            $list[$k]['result'] = json_encode($result['startArg']);

            if(strpos($v['uri'],'tracker') !== false||strpos($v['uri'],'getTracker') !== false){
                $list[$k]['data'] ='';
            }else{
                try{
                    $data = $list[$k]['return_data'];
                    $data = json_decode($data,true);
                    if ((is_array($data) && !empty($data))) {
                        $list[$k]['data'] =$list[$k]['return_data'];
                    }else{
                        $list[$k]['data'] =stripslashes(nl2br(strip_tags($list[$k]['return_data'])));
                    }

                }catch (\Exception $e){
                    return json_encode($list[$k]);
                }


            }

        }

        $this->writeJson(Status::CODE_OK, ['total'=>$total,'list'=>$list,'sql'=>$model->lastQuery()->getLastPrepareQuery()], 'success');
        return true;

    }
    /**
     * 删除品牌
     */
    public function del(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            $data['is_deleted'] = 1;
            if( WechatModel::create()->where('id',$ids,'in')->destroy()){
                $this->writeJson(Status::CODE_OK, ['status'=>1], '删除账户成功');return false;
            }else{
                $this->writeJson(Status::CODE_OK, ['status'=>0], '删除账户失败');return false;
            }
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0,'param'=>$this->param], '请选择要删除的账户');
        }
        return false;
    }

}

