

create database shop8 charset=utf8;
use shop8;

-- 品牌表
drop table if exists hui_brand;
create table hui_brand
(
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '名称',
  logo varchar(255) not null default '' comment 'LOGO',
  site varchar(255) not null default '' comment '官网',
  sort int not null default 0 comment '排序',
  create_at int not null default 0 comment '创建时间',
  update_at int not null default 0 comment '修改时间',
  key (title),
  key (sort),
  key (update_at)
) engine innodb charset utf8 comment '品牌';



-- 管理员
create table hui_admin
(
  id int unsigned auto_increment primary key comment 'ID',
  user varchar(32) not null default '' comment '管理员',
  password varchar(64) not null default '' comment '密码',
  salt varchar(12) not null default '' comment '盐', -- 混淆字符串
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  unique index (user),
  index (password(10))
) engine innodb charset utf8 comment '管理员';

insert into hui_admin values (1, 'admin', md5(concat('123456', '7a8f')), '7a8f', unix_timestamp(), unix_timestamp
());
insert into hui_admin values (2, 'admin123', md5(concat('13456', '000000')), '000000', unix_timestamp(), unix_timestamp
());


-- 角色(分组)
drop table if exists hui_auth_group;
create table hui_auth_group
(
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(16) not null default '' comment '组(角色)',
  is_super tinyint not null default 0 comment '是否超级管理员',
  sort int not null default 0 comment '顺序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (sort),
  index (title)
) engine innodb charset utf8 comment '组(角色)';


-- 规则(动作)
create table hui_auth_rule
(
  id int unsigned auto_increment primary key comment 'ID',
  name varchar(64) not null default '' comment '标识',
  title varchar(16) not null default '' comment '规则',
  sort int not null default 0 comment '顺序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (sort),
  index (name)
) engine innodb charset utf8 comment '规则';

-- 管理员分组
drop table if exists hui_auth_group_admin;
create table hui_auth_group_admin
(
  id int unsigned auto_increment primary key comment 'ID',
  admin_id int unsigned not null default 0 comment '管理员',
  group_id int unsigned not null default 0 comment '分组',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  unique key (admin_id, group_id),
  index (group_id)
) engine innodb charset utf8 comment '用户和组关系';

-- 规则与分组
drop table if exists hui_auth_group_rule;
create table hui_auth_group_rule
(
  id int unsigned auto_increment primary key comment 'ID',
  rule_id int unsigned not null default 0 comment '规则',
  group_id int unsigned not null default 0 comment '分组',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  unique key (group_id, rule_id),
  index (rule_id)
) engine innodb charset utf8 comment '规则和组关系';


-- 产品表
drop table if exists hui_product;
create table hui_product (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(64) not null default '' comment '名称',
  upc varchar(255) not null default '' comment '通用代码', -- 通用商品代码
  image varchar(255) not null default '' comment '图像',
  image_thumb varchar(255) not null default '' comment '缩略图',
  quantity int unsigned not null default 0 comment '库存', -- 库存
  sku_id int unsigned not null default 0 comment '库存单位', -- 库存单位
  stock_status_id int unsigned not null default 0 comment '库存状态', -- 库存状态ID
  is_subtract tinyint not null default 0 comment '扣减库存', -- 是否减少库存
  price decimal(10, 2) not null default 0.0 comment '售价',
  price_origin decimal(10, 2) not null default 0.0 comment '原价',
  minimum int unsigned not null default 0 comment '最少起售', -- 最小起订数量
  is_shipping tinyint not null default 0 comment '配送支持', -- 是否允许配送
  date_available timestamp not null default current_timestamp comment '起售时间', -- 供货日期
  length int unsigned not null default 0 comment '长',
  width int unsigned not null default 0 comment '宽',
  height int unsigned not null default 0 comment '高',
  length_unit_id int unsigned not null default 0 comment '长度单位', -- 长度单位
  weight int unsigned not null default 0 comment '重量',
  weight_unit_id int unsigned not null default 0 comment '重量单位', -- 重量的单位
  tax_id int unsigned not null default 0 comment '税类型', -- 税类型ID
  is_on_sale tinyint not null default 0 comment '上架', -- 是否可用
  description text comment '描述', -- 商品描述
  sort int not null default 0 comment '排序', -- 排序
  brand_id int unsigned not null default 0 comment '品牌', -- 所属品牌ID
  is_deleted tinyint not null default 0 comment '是否被删除', -- 是否被删除
  type_id int unsigned not null default 0 comment '属性组',
  group_id int unsigned not null default 0 comment '所属组',
  static_url varchar(255) not null default '' comment '静态URL',
  -- SEO优化
  meta_title varchar(255) not null default '' comment 'SEO标题',
  meta_keywords varchar(255) not null default '' comment 'SEO关键字',
  meta_description varchar(1024) not null default '' comment 'SEO描述',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  unique key (upc),
  index (brand_id),
  index (sku_id),
  index (tax_id),
  index (stock_status_id),
  index (length_unit_id),
  index (weight_unit_id),
  index (sort),
  index (price),
  index (quantity)
) engine innodb charset utf8 comment '产品';


-- 库存状态
drop table if exists hui_stock_status;
create table hui_stock_status (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '库存状态',
  sort int not null default 0 comment '排序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  index (sort)
) engine=innodb charset=utf8 comment='库存状态';
-- 参考测试数据
insert into hui_stock_status values (1, '库存充足', 0, unix_timestamp(), unix_timestamp());
insert into hui_stock_status values (2, '脱销', 0, unix_timestamp(), unix_timestamp());
insert into hui_stock_status values (3, '预定', 0, unix_timestamp(), unix_timestamp());
insert into hui_stock_status values (4, '1至3周销售', 0, unix_timestamp(), unix_timestamp());
insert into hui_stock_status values (5, '1至3天销售', 0, unix_timestamp(),unix_timestamp());


-- 库存单位
drop table if exists hui_sku;
create table hui_sku (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '库存单位',
  sort int not null default 0 comment '排序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  key (title),
  key (sort)
) ENGINE=innodb charset=utf8 comment='库存单位';
-- 参考测试数据
insert into hui_sku values (1, '部', 0, unix_timestamp(), unix_timestamp());
insert into hui_sku values (2, '台', 0, unix_timestamp(), unix_timestamp());
insert into hui_sku values (3, '只', 0, unix_timestamp(), unix_timestamp());
insert into hui_sku values (4, '条', 0, unix_timestamp(), unix_timestamp());
insert into hui_sku values (5, '头', 0, unix_timestamp(), unix_timestamp());

drop table if exists hui_length_unit;
create table hui_length_unit (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '长度单位',
  sort int not null default 0 comment '排序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  index (sort)
) engine=innodb charset=utf8 comment='长度单位';
-- 参考测试数据
insert into hui_length_unit values (1, '厘米', 0, unix_timestamp(), unix_timestamp());
insert into hui_length_unit values (2, '毫米', 0, unix_timestamp(), unix_timestamp());
insert into hui_length_unit values (3, '米', 0, unix_timestamp(), unix_timestamp());
insert into hui_length_unit values (4, '千米', 0, unix_timestamp(), unix_timestamp());
insert into hui_length_unit values (5, '英寸', 0, unix_timestamp(), unix_timestamp());


-- 重量单位
drop table if exists hui_weight_unit;
create table hui_weight_unit (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '重量单位',
  sort int not null default 0 comment '排序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  index (sort)
) engine=innodb charset=utf8 comment='重量单位';
-- 参考测试数据
insert into hui_weight_unit values (1, '克', 0, unix_timestamp(), unix_timestamp());
insert into hui_weight_unit values (2, '千克', 0, unix_timestamp(), unix_timestamp());
insert into hui_weight_unit values (3, '克拉', 0, unix_timestamp(), unix_timestamp());
insert into hui_weight_unit values (4, '市斤', 0, unix_timestamp(), unix_timestamp());
insert into hui_weight_unit values (5, '吨', 0, unix_timestamp(), unix_timestamp());
insert into hui_weight_unit values (6, '磅', 0, unix_timestamp(), unix_timestamp());

-- 税类型
drop table if exists hui_tax;
create table hui_tax (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '税类型',
  sort int not null default 0 comment '排序',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  index (sort)
) engine=innodb charset=utf8 comment='税类型';
-- 参考测试数据
insert into hui_tax values (1, '免税产品', 0, unix_timestamp(), unix_timestamp());
insert into hui_tax values (2, '缴税产品', 0, unix_timestamp(), unix_timestamp());
insert into hui_tax values (3, '反税产品', 0, unix_timestamp(), unix_timestamp());




-- 商品分类表
drop table if exists hui_category;
create table hui_category (
  id int unsigned auto_increment primary key comment 'ID',
  title varchar(32) not null default '' comment '分类',
  parent_id int unsigned not null default 0 comment '上级分类',
  sort int not null default 0 COMMENT '排序',
  image varchar(255) not null default '' comment '图片', -- 分类图片
  image_thumb varchar(255) not null default '' comment '缩略图', -- 分类图片缩略图
  is_used boolean not null default 0 comment '启用', -- tinyint(1)
  -- SEO优化
  meta_title varchar(255) not null default '' comment 'SEO标题',
  meta_keywords varchar(255) not null default '' comment 'SEO关键字',
  meta_description varchar(1024) not null default '' comment 'SEO描述',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  index (title),
  index (parent_id),
  index (sort)
) engine innodb charset utf8 comment '分类';

insert into hui_category values (1, '未分类', 0, -1, '', '', 0, '', '', '', unix_timestamp(), unix_timestamp());

drop table if exists hui_category_product;
create table hui_category_product(
  id int unsigned auto_increment primary key comment 'ID',
  category_id int unsigned not null default 0 comment '分类',
  product_id int unsigned not null default 0 comment '产品',
  create_time int not null default 0 comment '创建时间',
  update_time int not null default 0 comment '修改时间',
  unique key (category_id, product_id),
  key (product_id)
)engine innodb charset utf8 comment '分类关联产品';