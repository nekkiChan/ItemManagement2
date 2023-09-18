<?php

return [
    'failed' => '認証情報が一致しません。',
    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認用）',
        'type' => '種別',
    ],
    'custom' => [
        'email' => [
            'unique' => 'そのメールアドレスは既に登録されています。',
        ],
        'password' => [
            'confirmed' => 'パスワードは少なくとも8文字で、確認用と一致する必要があります。',
        ],
    ],
    'required' => ':attribute は必須です。',
    'max' => [
        'string' => ':attribute は :max 文字以下で入力してください。',
    ],
];


?>
