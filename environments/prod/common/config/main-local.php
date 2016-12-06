<?php
return [
    'homeUrl'=> ['caderno-edicoes'],
    'name' => 'IMPRENSA OFICIAL',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=abio',
            'username' => 'root',
            'password' => '@by02016Abio#',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
