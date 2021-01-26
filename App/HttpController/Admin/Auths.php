<?php

namespace App\HttpController\Admin;

use App\HttpController\Common\Menu;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class SiamAuth
 * Create With Automatic Generator
 */
class Auths extends Base
{
    public function get_menu()
    {
        $menu[] = ["auth_rules" => "/", "auth_name" => "首页", "auth_icon" => "layui-icon-home"];
//        $menu[] = ['auth_id' => 10, "auth_rules" => "/", "auth_name" => "用户管理", "auth_icon" => "layui-icon-user",
//            'childs' => [["auth_id" => 11, "auth_name" => "用户列表", "auth_rules" => "/user/list", "auth_icon" => "", "auth_type" => 1]]
//        ];

        $menu[] = ['auth_id' => 20, "auth_rules" => "/admin/*/*", "auth_name" => "栏目管理", "auth_icon" => "layui-icon-propertysafety",
            'childs' => [
                ["auth_id" => 21, "auth_name" => "栏目列表", "auth_rules" => "/category/list", "auth_icon" => "", "auth_type" => 1],

            ]
        ];
        $menu[] = ['auth_id' => 50, "auth_rules" => "/admin/*/*", "auth_name" => "秘钥管理", "auth_icon" => "layui-icon-file-word",
            'childs' => [
                ["auth_id" => 51, "auth_name" => "产品秘钥列表", "auth_rules" => "/secret/list", "auth_icon" => "", "auth_type" => 1],
            ]
        ];

//        $menu[] = ['auth_id' => 70, "auth_rules" => "/admin/*/*", "auth_name" => "推广管理【待开发】", "auth_icon" => "layui-icon-folder-open",
//            'childs' => [
//                ["auth_id" => 71, "auth_name" => "推广链接列表", "auth_rules" => "/spread/list", "auth_icon" => "", "auth_type" => 1],
//            ]
//        ];
        $menu[] = ['auth_id' => 30, "auth_rules" => "/admin/*/*", "auth_name" => "订单管理", "auth_icon" => "layui-icon-filedone",
            'childs' => [
                ["auth_id" => 31, "auth_name" => "订单列表", "auth_rules" => "/order/list", "auth_icon" => "", "auth_type" => 1],
            ]
        ];
//        $menu[] = ['auth_id' => 60, "auth_rules" => "/admin/*/*", "auth_name" => "访问来源", "auth_icon" => "layui-icon-layout",
//            'childs' => [
//                ["auth_id" => 61, "auth_name" => "访问来源列表", "auth_rules" => "/keyword/list", "auth_icon" => "", "auth_type" => 1],
//                ["auth_id" => 62, "auth_name" => "来源关键词排行", "auth_rules" => "/keyword/rank", "auth_icon" => "", "auth_type" => 1]
//            ]
//        ];
        $menu[] = ['auth_id' => 40, "auth_rules" => "/admin/*/*", "auth_name" => "访问记录", "auth_icon" => "layui-icon-radarchart",
            'childs' => [
                ["auth_id" => 41, "auth_name" => "访问记录列表", "auth_rules" => "/tracker/list", "auth_icon" => "", "auth_type" => 1]
            ]
        ];
        $menu[] = ['auth_id' => 90, "auth_rules" => "/admin/*/*", "auth_name" => "系统管理", "auth_icon" => "layui-icon-setting",
            'childs' => [
                ["auth_id" => 91, "auth_name" => "系统配置", "auth_rules" => "/system/setting", "auth_icon" => "", "auth_type" => 1]
            ]
        ];
        //$menu = json_decode($tree);
        $this->writeJson(Status::CODE_OK,$menu , "success");
        return true;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        switch ($action) {
            case 'save_tree_list':
                $valitor = new Validate();
                $valitor->addColumn('order')->required();
                return $valitor;
                break;
        }
        return null;
    }

}

