-- 收货地址表
CREATE TABLE IF NOT EXISTS `address` (
  `id`       INT AUTO_INCREMENT PRIMARY KEY,
  `uid`      INT NOT NULL
  COMMENT '用户id',
  `address`  VARCHAR(255) COMMENT '收货地址',
  `area`     VARCHAR(255) COMMENT '地区',
  `realname` VARCHAR(255) COMMENT '姓名',
  `tel`      INT COMMENT '电话'
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

-- 购物车表
create table if not exists `cart`(
  `id`       int auto_increment  	primary key,
  `uid`      int not null comment '用户id',
  `gid`      int not null comment '商品id',
  `quantity` int not null comment '商品数量'
)engine=MyISAM default charset=utf8;

-- 订单表
create table if not exists `order`(
  `id`          int        auto_increment  	primary key,
  `ordernum`    varchar(255) unique not null comment '订单编号',
  `uid`         int                 not null comment '用户id',
  `aid`         int                 not null comment '收货信息id',
  `addtime`     time  comment '下单时间',
  `paymenttype` tinyint(1) default 1 comment '支付方式 1-货到付款 2-微信 3-支付宝',
  `status`      tinyint(1) default 1 comment '订单状态 -1-已取消 1-待支付 2-待发货 3-待收货 4-退款中 5-已退款 6-已完成',
  `ispay`       tinyint(1) default 2 comment '1- 已支付 2-未支付'
)engine=MyISAM default charset=utf8;

-- 订单详情表
create table if not exists `orderdetails`(
  `id`       int auto_increment  	primary key,
  `oid`      int    comment '订单id',
  `gid`      int    comment '商品id',
  `price`    decimal(10,2) comment '商品价格',
  `quantity` int not null comment '商品数量'
)engine=MyISAM default charset=utf8;

-- 收货地址表
create table if not exists `cart`(
  `id`       int auto_increment  	primary key,
  `uid`      int not null comment '用户id',
  `address`  varchar(255)    comment '收货详情地址',
  `realname` varchar(40) comment '收货人姓名',
  `mobile`   int comment '电话',
  `area`     varchar(255) comment '所在地区'
)engine=MyISAM default charset=utf8;

-- 评论表
CREATE TABLE IF NOT EXISTS `comments` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `uid`       INT NOT NULL
  COMMENT '用户id',
  `comments`  VARCHAR(255) COMMENT '评论',
  `gid`       INT COMMENT '商品id',
  `commentid` INT COMMENT '回复哪条评论id',
  `status`    INT COMMENT '回复状态 1管理员回复 2管理员未回复'
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;