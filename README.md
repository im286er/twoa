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
* alter table oa_attend_checkin add acheckin_state int(2) default 0;

#### 2017-8-14
* alter table oa_attend_checkin add acheckin_temptime float(4,2) default 0.00;

#### 2017-8-15
* 修改外勤计算功能
* ALTER TABLE `oa_attend_checkin` CHANGE `acheckin_temptime` `acheckin_tempstorage` FLOAT(4,2) NULL DEFAULT '0.00';
* ALTER TABLE `oa_attend_checkin` CHANGE `acheckin_tempstorage` `acheckin_tempstorage` VARCHAR(200) NULL DEFAULT '';

#### 2017-8-21
* alter table oa_attend_checkin add acheckin_applyid bigint(20) default 0;

#### 2017-8-23
* 考勤申请页面
* 修改menu侧栏样式
* 新增url路径参数支持直接显示功能

#### 2017-8-31
* CREATE TABLE oa_project_list (project_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'id',project_company int(9) NOT NULL COMMENT '公司名称', project_name varchar(200) NOT NULL COMMENT '项目名称',project_type int(9) default 0 COMMENT '项目类型',project_startdate date COMMENT '开始时间',project_enddate date COMMENT '结束时间',project_state int(1) COMMENT '状态');

#### 2017-09-02
* ALTER TABLE `oa_project_list` ADD `project_captain` BIGINT(10) NOT NULL DEFAULT '0' COMMENT '担当' AFTER `project_enddate`, ADD `project_region` INT(5) NOT NULL DEFAULT '0' COMMENT '地区' AFTER `project_captain`;
* ALTER TABLE `oa_attend_apply` CHANGE `aapply_project` `aapply_project` BIGINT(20) NULL DEFAULT '0' COMMENT '涉及项目';

#### 2017-09-08
* 注销 chosen.jquery.js文件：if (/iP(od|hone)/i.test(window.navigator.userAgent) ……让其支持移动设备
* 修改 elements.aside.js文件 content.css('max-height', content.find(".modal-body").height()+70+'px');让内容高度随着modal-body变化
* 修改 elements.aside.js文件 $modal.appendTo(this.container || 'body'); //修改防止容器append到body中
* 新增跳链接，复制url到浏览器，登录后会自动跳转到对应的链接

#### 2017-09-13
* ALTER TABLE `oa_attend_apply` ADD `aapply_tempstorage` VARCHAR(1000) NULL DEFAULT '' COMMENT '临时储存考勤计算' ;

#### 2017-09-14
* ALTER TABLE `oa_attend_apply` ADD `aapply_settle` INT(2) NOT NULL DEFAULT '0' COMMENT '申请是否结算' ;

#### 2017-09-19
* ALTER TABLE `oa_project_list` ADD `project_trave` INT(2) NOT NULL DEFAULT '0' COMMENT '是否出差' AFTER `project_type`;

#### 2017-09-29
* 完成考勤审计
* 完成考勤月历js部分
#### 2017-09-30
* 完成考勤月历
* 修复头像显示
#### 2017-10-5
* 考勤高级管理界面
#### 2017-10-09
* 使用jquery.media 读取pdf和img
#### 2017-10-10
* 完成工作日月历添加修改
* 考勤员工列表修改
#### 2017-10-12
* 完成考勤记录查询修改
#### 2017-10-13
* 完成打卡记录查改
#### 2017-10-16
* 考勤高级配置完成
