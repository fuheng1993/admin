//请求URL
layui.define([], function (exports) {
    exports('api', {
        Index: 'index/index',
        login: 'login/login',
        updatePassword:'login/updatePassword',
        getMenu: 'auths/get_menu',
        getGoods: 'json/goods.js',
        // 权限管理
        getAuthList: 'auths/get_tree_list',
        saveAuthList: 'auths/save_tree_list',
        addAuth: 'auths/add',
        getAllAuth: 'auths/getAll',

        // 角色管理
        addRole: 'roles/add',
        getAllRole: 'roles/getAll',
        getRoleInfo: 'roles/getOne',
        ediRole: 'roles/update',
        delRole: 'roles/delete',

        // 系统管理
        clearCache: 'system/clearCache',
        showCache: 'system/showCache',
        //统计
        getTodayUser:'chart/getTodayUser',
        getDayChart:'chart/getDayChart',
        getMonthChart:'chart/getMonthChart',
        getPayChart:'chart/getPayChart',
        getPayBieChart:'chart/getPayBieChart',
        getTableChart:'chart/getTableChart',

        //用户管理
        getUserList:'user/lists',
        addUser:'user/add',
        editUser:'user/edit',
        delUser:'user/del',
        doUserStatus:'user/doStatus',
        doUserIsTest:'user/doIsTest',
        getMyWechat:'user/getMyWechat',
        getAllUser:'user/getAllUser',
        getTodayChart:'order/getTodayChart',
        //秘钥管理
        getSecretList:'secret/lists',
        addSecret:'secret/add',
        editSecret:'secret/edit',
        delSecret:'secret/del',
        doSecretStatus:'secret/doStatus',
        getSecretAll:'secret/all',

        //栏目管理
        getCategoryList:'category/lists',
        addCategory:'category/add',
        editCategory:'category/edit',
        delCategory:'category/del',
        doCategoryStatus:'category/doStatus',
        getCategoryAll:'category/all',
        //文章管理
        getArticleList:'article/lists',
        addArticle:'article/add',
        editArticle:'article/edit',
        delArticle:'article/del',
        //评论管理
        getCommentList:'comment/lists',
        addComment:'comment/add',
        editComment:'comment/edit',
        delComment:'comment/del',
        //小程序管理
        getAppletList:'applet/lists',
        addApplet:'applet/add',
        editApplet:'applet/edit',
        delApplet:'applet/del',
        getAllApplet:'applet/getAllApplet',

        //订单管理
        getOrderList:'order/lists',
        getOrderDetails:'order/details',
        delOrder:'order/del',
        sendOrder:'order/send',
        finishOrder:'order/finish',
        refundOrder:'order/refund',
        doOcpc:'order/doOcpc',
        //访问来源
        getKeywordList:'keyword/lists',
        getKeywordRank:'keyword/rank',



        //系统配置
        getSystem:'system/getSystem',
        saveSystem:'system/saveSystem',
        saveEmail:'system/saveEmail',
        sendMessageTest:'system/sendMessageTest',
        systemUpload:'system/upload',
        //链路最终
        getTracker:'tracker/lists',
        //邮件消息管理
        getMessage:'message/lists',
        sendMessage:'message/send',
        //订单异步通知消息
        getNotify:'notify/lists',
        sendNotify:'notify/send',
    });
});