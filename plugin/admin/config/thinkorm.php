<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => '122.114.74.62',
            // 数据库名
            'database' => '0607duanju_62_hz',
            // 数据库用户名
            'username' => '0607duanju_62_hz',
            // 数据库密码
            'password' => 'eAQQRJZEtCew2Q3K',
            // 数据库连接端口
            'hostport' => 3306,
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => 'utf8mb4',
            // 数据库表前缀
            'prefix' => '',
            // 断线重连
            'break_reconnect' => true,
            // 关闭SQL监听日志
            'trigger_sql' => true,
            // 自定义分页类
            'bootstrap' =>  ''
        ],
    ],
];