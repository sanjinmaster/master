<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

/**
 * 后台管理系统
 */

// 上传图片
Route::post('uploadFile','common/upload/uploadFile');
// 上传字符串
Route::post('strFileUpload','common/upload/strFileUpload');

// 登录
Route::get('admin/login','petadmin/login/login');
// 退出
Route::delete('admin/loginOut','petadmin/login/loginOut');

// 报表管理-订单统计
Route::get('admin/order/report','petadmin/index/orderReport');
// 报表管理-资金统计
Route::get('admin/amount/report','petadmin/index/amountReport');
// 报表管理-流量统计
Route::get('admin/flow/report','petadmin/index/flowReport');

// 服务管理-上门疫苗、体检、美容、火化-列表
Route::get('admin/service/list','petadmin/service/serviceRun');
// 服务管理-上门疫苗、体检、美容、火化-添加商品
Route::post('admin/service/addGoods','petadmin/service/addGoods');
// 服务管理-查看编辑
Route::get('admin/service/edit','petadmin/service/editLook');
// 服务管理-查看编辑保存
Route::put('admin/service/editAction','petadmin/service/editAction');
// 服务管理-下架
Route::patch('admin/service/lower','petadmin/service/lowerXj');
// 服务管理-删除
Route::delete('admin/service/del','petadmin/service/delGoods');

// banner管理-上传
Route::post('admin/banner/add','petadmin/Banner/addBanner');
// banner管理-列表
Route::get('admin/banner/list','petadmin/Banner/bannerList');
// banner管理-更换
Route::put('admin/banner/switch','petadmin/Banner/updateBanner');
// banner管理-删除
Route::delete('admin/banner/del','petadmin/Banner/delBanner');

// 订单管理-列表
Route::get('admin/order/list','petadmin/Order/orderList');
// 订单管理-查看
Route::get('admin/order/look','petadmin/Order/orderDetails');
// 订单管理-删除
Route::delete('admin/order/del','petadmin/Order/delOrder');

// 资料管理-分类管理-添加分类
Route::post('admin/data/addClass','petadmin/Dictionaries/addZdClass');
// 资料管理-分类管理-列表
Route::get('admin/data/list','petadmin/Dictionaries/zdList');
// 资料管理-分类管理-编辑查看
Route::get('admin/data/editLook','petadmin/Dictionaries/editLook');
// 资料管理-分类管理-编辑保存
Route::put('admin/data/editSave','petadmin/Dictionaries/editSave');
// 资料管理-分类管理-删除
Route::delete('admin/data/del','petadmin/Dictionaries/delZd');
// 资料管理-资料列表-添加资料--显示分类
Route::get('admin/details/getClassMeans','petadmin/Means/getClassMeans');
// 资料管理-资料列表-添加资料
Route::post('admin/details/saveData','petadmin/Means/addMeans');
// 资料管理-资料列表-列表
Route::get('admin/details/list','petadmin/Means/meansList');
// 资料管理-资料列表-上、下架首页
Route::put('admin/details/upDownFrame','petadmin/Means/upDownFrame');
// 资料管理-资料列表-查看编辑
Route::get('admin/details/editLook','petadmin/Means/editLook');
// 资料管理-资料列表-查看编辑保存
Route::put('admin/details/editSave','petadmin/Means/editSave');
// 资料管理-资料列表-删除
Route::delete('admin/details/del','petadmin/Means/delMeans');

// 用户管理-用户列表-列表
Route::get('admin/user/list','petadmin/User/userList');
// 用户管理-用户列表-冻结、恢复
Route::put('admin/user/offNo','petadmin/User/offNo');
// 用户管理-用户列表-删除
Route::delete('admin/user/del','petadmin/User/delUser');
// 用户管理-医生列表-列表
Route::get('admin/doctor/list','petadmin/Doctor/doctorList');
// 用户管理-医生列表-冻结、恢复
Route::put('admin/doctor/offNo','petadmin/Doctor/offNo');
// 用户管理-医生列表-删除
Route::delete('admin/doctor/del','petadmin/Doctor/delDoctor');
// 用户管理-医院列表-列表
Route::get('admin/hospital/list','petadmin/Hospital/hospitalList');
// 用户管理-医院列表-冻结、恢复
Route::put('admin/hospital/offNo','petadmin/Hospital/offNo');
// 用户管理-医院列表-删除
Route::delete('admin/hospital/del','petadmin/Hospital/delHospital');

// 意见反馈-列表
Route::get('admin/feedback/list','petadmin/Idea/ideaList');
// 意见反馈-查看
Route::get('admin/feedback/look','petadmin/Idea/lookIdea');
// 意见反馈-删除
Route::delete('admin/feedback/del','petadmin/Idea/delIdea');

// 提现管理-列表
Route::get('admin/take/list','petadmin/Take/takeList');
// 提现管理-同意、拒绝
Route::put('admin/take/agreeNo','petadmin/Take/agreeNo');
// 提现管理-删除
Route::delete('admin/take/del','petadmin/Take/delTakeTal');


//----------------------------------------------------------------------------------------------------------------------


/**
 *  小程序前台
 */

// login
Route::get('openid','index/login/getWxOpenid');
Route::get('login','index/login/wxLogin');

// banner轮播图
Route::get('banner','index/banner/banner');

// user用户
Route::get('user','index/user/user');
Route::get('users','index/user/users');
Route::post('users','index/user/createUser');
Route::put('users','index/user/updateUser');
Route::delete('users','index/user/deleteUser');

// 用户地址
Route::get('address','index/address/address');
Route::get('addresses','index/address/addresses');
Route::post('addresses','index/address/createAddress');
Route::put('addresses','index/address/updateAddress');
Route::delete('addresses','index/address/deleteAddress');

// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表
Route::get('pet/home/goods','index/Service/goodsList');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-商品详情
Route::get('pet/home/goodsDetails','index/Service/goodsDetails');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-搜索
Route::get('pet/home/search','index/Service/search');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-购物车、商品添加数量
Route::post('pet/home/addShopCar','index/Cart/addShopCar');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-购物车、商品减少数量
Route::put('pet/home/reduceShopCar','index/Cart/reduceShopCar');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-购物车列表
Route::get('pet/home/shopCarList','index/Cart/shopCarList');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表、购物车-去结算
Route::post('pet/home/setAmountCart','index/Settlement/setAmountCart');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品详情-去结算
Route::post('pet/home/goodsDetailsAmount','index/Settlement/goodsDetailsAmount');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-结算-获取默认地址
Route::get('pet/home/getDefaultAddress','index/Settlement/getDefaultAddress');

// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-去支付
Route::post('pet/home/pay','index/Pay/makePay');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-支付回调
Route::put('pet/home/notifyBack','index/WxNotifyBack/notifyBack');

// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-去结算,去砍价
Route::post('pet/home/bargain','index/Bargain/bargain');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-砍价活动,查看详情
Route::get('pet/home/orderDetails','index/banner/banner');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-砍价活动,活动规则
Route::get('pet/home/about','');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-砍价活动,让朋友也来砍一刀
Route::post('pet/home/share','index/Bargain/makeAmount');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-砍价活动,现在就要去付款
Route::put('pet/home/now','index/banner/banner');
// 首页-业务分类(上门疫苗、上门体检、上门美容、上门火化)-商品列表-砍价活动,取消此订单
Route::delete('pet/home/cancelOrder','index/banner/banner');

// 首页-宠物字典,默认三个字典
Route::get('pet/home/petThree','index/Petthree/indexThree');
// 首页-宠物字典详情
Route::get('pet/home/petDetails','index/Petthree/petDetails');

// 咨询-宠物字典分类列表
Route::get('pet/speak/petList','index/Petthree/petList');
// 咨询-宠物字典列表
Route::get('pet/speak/zxList','index/Petthree/zxList');
// 咨询-宠物字典搜索
Route::get('pet/speak/search','index/Petthree/petSearch');

// 购物车-删除商品
Route::delete('pet/car/del','index/Cart/delGoods');

// 我的-头部个人信息
Route::get('pet/my/personalInfo','index/My/personalInfo');
// 分享
Route::post('pet/my/sharePet','index/My/sharePet');
// 我的-头部个人信息-下级详情
Route::get('pet/my/nextDetails','index/banner/banner');
// 我的-头部个人信息-奖励金详情
Route::get('pet/my/rewardDetails','index/My/rewardDetails');
// 我的-头部个人信息-奖励金提现
Route::post('pet/my/rewardOut','index/My/rewardOut');
// 我的-头部个人信息-奖励金收支明细
Route::get('pet/my/moneyDetails','index/My/moneyDetails');
// 绑定支付宝账号
Route::post('pet/my/bindAliPay','index/My/bindAliPay');
// 我的-头部个人信息-优惠券详情
Route::get('pet/my/couponDetails','index/My/getCoupon');
// 我的-我的订单-状态详情(待付款、待接单、待服务、待确认、已完成)
Route::get('pet/my/statusDetails','index/Order/orderStatus');
// 我的-我的订单-订单详情(待付款、待接单、待服务、待确认、已完成)
Route::get('pet/my/orderDetails','index/Order/orderDetails');
// 我的-我的订单-取消订单(待付款、待接单、待服务)
Route::delete('pet/my/cancelOrder','index/banner/banner');
// 我的-我的订单-待付款-立即支付
Route::put('pet/my/pay','index/banner/banner');
// 我的-我的订单-待服务-查看地图
Route::get('pet/my/lookMap','index/banner/banner');
// 我的-我的订单-医生信息
Route::get('pet/my/doctorInfo','index/Order/doctorPjInfo');
// 我的-我的订单-待确认-确认订单
Route::put('pet/my/confirmOrder','index/Order/confirmOrder');
// 我的-我的订单-待服务-删除订单,待付款
Route::delete('pet/my/cancelOrderDfk','index/Order/cancelOrderDfk');
// 我的-我的订单-待服务-删除订单,待接单、待服务
Route::post('pet/my/cancelOrderDjdDfw','index/Pay/cancelOrderDjdDfw');
// 取消回调
Route::post('pet/my/cancelBack','index/WxNotifyBack/cancelBack');
// 我的-我的订单-待服务-去评价
Route::post('pet/my/evaluateNote','index/Order/evaluateNote');
// 我的-我的服务-意见反馈
Route::post('pet/my/feedback','index/My/ideaBack');


//----------------------------------------------------------------------------------------------------------------------


/**
 *  APP医生端
 */

// 发送验证码
Route::post('app/sendCode','api/Register/send');
// 注册-医院
Route::post('app/hospital/register','index/banner/banner');
// 注册-医院-资料填写
Route::post('app/hospital/addData','index/banner/banner');
// 注册-医生
Route::post('app/doctor/register','index/banner/banner');
// 注册-医生-资料填写
Route::post('app/doctor/addData','index/banner/banner');
// 登录-医院、医生
Route::post('app/login','index/banner/banner');
// 忘记密码-医院、医生
Route::put('app/forgetPwd','index/banner/banner');

// 接单-认证过的医生-列表(待接单、待服务、待确认、已完成)
Route::get('app/order/orderList','index/banner/banner');
// 接单-认证过的医生-待接单-接单、弃单
Route::post('app/order/agreeOrder','index/banner/banner');
// 接单-认证过的医生-列表订单详情(待接单、待服务、待确认、已完成)
Route::get('app/order/orderDetails','index/banner/banner');
// 接单-认证过的医生-待服务-确认联系
Route::get('app/order/waitSer','index/banner/banner');
// 接单-认证过的医生-待服务-出发服务
Route::put('app/order/goSer','index/banner/banner');
// 接单-认证过的医生-已完成-删除订单
Route::delete('app/order/delOrder','index/banner/banner');
// 接单-医院-订单列表(待接单、待服务、待确认、已完成)
Route::get('app/order/doctorOrderList','index/banner/banner');
// 接单-医院-待接单-接单、弃单
Route::post('app/order/agrOrderNo','index/banner/banner');
// 接单-医院-待接单-接单-选择医生
Route::post('app/order/selDoctor','index/banner/banner');
// 接单-医院下面的医生-待接单-确认
Route::post('app/order/agrOrder','index/banner/banner');
// 接单-医院下面的医生-待服务-确认联系
Route::get('app/order/agrTel','index/banner/banner');

// 我的-医院-基础信息(包含头像、医院名称、联系方式、账户金额、工作状态)
Route::get('app/my/hosInfo','index/banner/banner');
// 我的-医院-医院信息-更换头像
Route::put('app/my/switchImg','index/banner/banner');
// 我的-医院-医院信息-修改手机号
Route::put('app/my/updateMobile','index/banner/banner');
// 我的-医院-医院信息-修改登录密码
Route::put('app/my/updatePass','index/banner/banner');
// 我的-医院-账户-列表
Route::get('app/my/hosListAmt','index/banner/banner');
// 我的-医院-账户-提现
Route::post('app/my/takeAmt','index/banner/banner');
// 我的-医院-账户-收支明细
Route::get('app/my/budget','index/banner/banner');
// 我的-医院-账户-绑定支付宝
Route::put('app/my/bindPay','index/banner/banner');
// 我的-医院-账户-工作中
Route::put('app/my/isWork','index/banner/banner');
// 我的-医院-账户-工作中
Route::get('app/my/evaluate','index/banner/banner');
// 我的-医院-我的医生
Route::get('app/my/myDoctor','index/banner/banner');
// 我的-医院-添加医生
Route::post('app/my/addDoctor','index/banner/banner');
// 我的-医生-头部信息
Route::get('app/my/headInfo','index/banner/banner');
// 我的-医生-修改昵称
Route::put('app/my/editNickname','index/banner/banner');
// 我的-医生-认证
Route::post('app/my/attestation','index/banner/banner');
// 我的-医生-所属医院
Route::get('app/my/belong','index/banner/banner');
// 我的-医生-加入医院
Route::post('app/my/joinHos','index/banner/banner');



