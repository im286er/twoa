### Twoway OA 系统2017
#### 主要技术
* PHP/Thinkphp
* Html/css/JavaScript/jquery
* ace模板

#### 说明
* 通过Thinkphp实现模块化开发
* 通过bootstrap实现响应式布局
* 通过微信企业号实现移动端管理
* ace模板让界面变得更优质

##### Admin
管理员对全站的管理

##### Home 
* 所有员工的入口
* 根据不同权限调用不同的控制器

### 更新说明

#### 2017-6-20
* 新增表oa_user user_subgroup 字段
* 新增员工注册页面

#### 2017-6-22
* 新增短信发送平台
* 注册页加入短信验证

#### 2017-7-5
* 完成基础信息添加

#### 2017-7-12
* 修改部门字段
* 调整部门和分组信息
* 完成基础信息

#### 2017-7-13
* 员工信息修改和添加
* 新增高层管理部门字段department_leader和place_extent

#### 2017-7-14
* 高层管辖部门增删改

#### 2017-7-19
* 修改权限判断和输出菜单

#### 2017-7-20
* 修改前后台各种权限显示

#### 2017-7-21
* 修改职位默认角色指定
* 修改角色指定页面权限
* 新建角色指定数据表权限
* 新增AmongModel中间模型

#### 2017-07-26
* 修改日期js显示中文

#### 2017-8-1
* 档案管理，增改

#### 2017-8-7
* 新增考勤模块
* 编写打卡功能
* 修改oa_rauth rauth_auth和rauth_table 字段数据类型为text

#### 2017-8-9
* 完善打卡页面
* 修改InfoModel.class
* 修改微信接口
* 修改gethtml和权限功能
* ALTER TABLE `oa_attend_record` CHANGE `arecord_json` `arecord_json` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '数据json格式';

#### 2017-8-10
* admin后台新建初始化设置新表的权限为select，超级管理员默认所有权限

