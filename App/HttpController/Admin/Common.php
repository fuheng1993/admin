<?php

namespace App\HttpController\Admin;


use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Common extends Base
{

    /**
     * 上传图片
     * @return bool
     */
    public function upload(){
        $request=  $this->request();
        $img_file = $request->getUploadedFile('file');//获取一个上传文件,返回的是一个\EasySwoole\Http\Message\UploadFile的对象
        $fileSize = $img_file->getSize();
        //上传图片不能大于5M (1048576*5)
        if($fileSize>1048576*5){
            $this->AjaxJson(0,['size'=>$fileSize], '图片最大不能超过5MB'); return false;
        }
        $clientFileName = $img_file->getClientFilename();
        $fileName = '_'.MD5(time()).'.'.pathinfo($clientFileName, PATHINFO_EXTENSION);;
        $res = $img_file->moveTo(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName);
        if(file_exists(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName)){
            $data['image'] = '/public/uploads/'.$fileName;
            $this->AjaxJson(1, $data, 'success');
        }else{
            $this->AjaxJson(0,[], '文件上传失败');
        }
        return true;
    }
    protected function getValidateRule(?string $action): ?Validate
    {
        // TODO: Implement getValidateRule() method.
        switch ($action) {
            case 'login':
                $valitor = new Validate();
                $valitor->addColumn('u_account')->required();
                $valitor->addColumn('u_password')->required();
                return $valitor;
                break;
        }
        return NULL;
    }
}

