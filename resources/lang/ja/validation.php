<?php

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行はバリデタークラスにより使用されるデフォルトのエラー
    | メッセージです。サイズルールのようにいくつかのバリデーションを
    | 持っているものもあります。メッセージはご自由に調整してください。
    |
    */

    'accepted' => ':attributeを承認してください。',
    'active_url' => ':attributeが有効なURLではありません。',
    // 'after' => ':attributeには、:dateより後の日付を指定してください。',
    'after' => ':attributeには、:dateより後の時間を指定してください。',
    'after_or_equal' => ':attributeには、:date以降の日付を指定してください。',
    'alpha' => ':attributeはアルファベットのみがご利用できます。',
    'alpha_dash' => ':attributeはアルファベットとダッシュ(-)及び下線(_)がご利用できます。',
    'alpha_num' => ':attributeはアルファベット数字がご利用できます。',
    'array' => ':attributeは配列でなくてはなりません。',
    'before' => ':attributeには、:dateより前の日付をご利用ください。',
    'before_or_equal' => ':attributeには、:date以前の日付をご利用ください。',
    'between' => [
        'numeric' => ':attributeは、:minから:maxの間で指定してください。',
        'file' => ':attributeは、:min kBから、:max kBの間で指定してください。',
        'string' => ':attributeは、:min文字から、:max文字の間で指定してください。',
        'array' => ':attributeは、:min個から:max個の間で指定してください。',
    ],
    'boolean' => ':attributeは、trueかfalseを指定してください。',
    'confirmed' => ':attributeと、確認フィールドとが、一致していません。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには、:dateと同じ日付けを指定してください。',
    'date_format' => ':attributeは:format形式で指定してください。',
    'different' => ':attributeと:otherには、異なった内容を指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'digits_between' => ':attributeは:min桁から:max桁の間で指定してください。',
    'dimensions' => ':attributeの図形サイズが正しくありません。',
    'distinct' => ':attributeには異なった値を指定してください。',
    'email' => ':attributeには、有効なメールアドレスを指定してください。',
    'ends_with' => ':attributeには、:valuesのどれかで終わる値を指定してください。',
    'exists' => '選択された:attributeは正しくありません。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeに値を指定してください。',
    'gt' => [
        'numeric' => ':attributeには、:valueより大きな値を指定してください。',
        'file' => ':attributeには、:value kBより大きなファイルを指定してください。',
        'string' => ':attributeは、:value文字より長く指定してください。',
        'array' => ':attributeには、:value個より多くのアイテムを指定してください。',
    ],
    'gte' => [
        'numeric' => ':attributeには、:value以上の値を指定してください。',
        'file' => ':attributeには、:value kB以上のファイルを指定してください。',
        'string' => ':attributeは、:value文字以上で指定してください。',
        'array' => ':attributeには、:value個以上のアイテムを指定してください。',
    ],
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => '選択された:attributeは正しくありません。',
    'in_array' => ':attributeには:otherの値を指定してください。',
    'integer' => ':attributeは整数で指定してください。',
    'ip' => ':attributeには、有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには、有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには、有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには、有効なJSON文字列を指定してください。',
    'lt' => [
        'numeric' => ':attributeには、:valueより小さな値を指定してください。',
        'file' => ':attributeには、:value kBより小さなファイルを指定してください。',
        'string' => ':attributeは、:value文字より短く指定してください。',
        'array' => ':attributeには、:value個より少ないアイテムを指定してください。',
    ],
    'lte' => [
        'numeric' => ':attributeには、:value以下の値を指定してください。',
        'file' => ':attributeには、:value kB以下のファイルを指定してください。',
        'string' => ':attributeは、:value文字以下で指定してください。',
        'array' => ':attributeには、:value個以下のアイテムを指定してください。',
    ],
    'max' => [
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'file' => ':attributeには、:max kB以下のファイルを指定してください。',
        'string' => ':attributeは、:max文字以下で指定してください。',
        'array' => ':attributeは:max個以下指定してください。',
    ],
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには、:min以上の数字を指定してください。',
        'file' => ':attributeには、:min kB以上のファイルを指定してください。',
        'string' => ':attributeは、:min文字以上で指定してください。',
        'array' => ':attributeは:min個以上指定してください。',
    ],
    'not_in' => '選択された:attributeは正しくありません。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeには、数字を指定してください。',
    'present' => ':attributeが存在していません。',
    'regex' => ':attributeに正しい形式を指定してください。',
    'required' => ':attributeは必ず指定してください。',
    'required_if' => ':otherが:valueの場合、:attributeも指定してください。',
    'required_unless' => ':otherが:valuesでない場合、:attributeを指定してください。',
    'required_with' => ':valuesを指定する場合は、:attributeも指定してください。',
    'required_with_all' => ':valuesを指定する場合は、:attributeも指定してください。',
    'required_without' => ':valuesを指定しない場合は、:attributeを指定してください。',
    'required_without_all' => ':valuesのどれも指定しない場合は、:attributeを指定してください。',
    'same' => ':attributeと:otherには同じ値を指定してください。',
    'size' => [
        'numeric' => ':attributeは:sizeを指定してください。',
        'file' => ':attributeのファイルは、:sizeキロバイトでなくてはなりません。',
        'string' => ':attributeは:size文字で指定してください。',
        'array' => ':attributeは:size個指定してください。',
    ],
    'starts_with' => ':attributeには、:valuesのどれかで始まる値を指定してください。',
    'string' => ':attributeは文字列を指定してください。',
    'timezone' => ':attributeには、有効なゾーンを指定してください。',
    'unique' => ':attributeの値は既に存在しています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeに正しい形式を指定してください。',
    'uuid' => ':attributeに有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | "属性.ルール"の規約でキーを指定することでカスタムバリデーション
    | メッセージを定義できます。指定した属性ルールに対する特定の
    | カスタム言語行を手早く指定できます。
    |
    */

    'custom' => [
        '属性名' => [
            'ルール名' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、例えば"email"の代わりに「メールアドレス」のように、
    | 読み手にフレンドリーな表現でプレースホルダーを置き換えるために指定する
    | 言語行です。これはメッセージをよりきれいに表示するために役に立ちます。
    |
    */

    'attributes' => [
        // 汎用
        'id' => 'No.',
        'name' => '名前',
        'name_kana' => '名前よみがな',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード確認',
        'date' => '日付',
        'created_at' => '作成日時',
        'updated_at' => '更新日時',
        'login_at' => '最終ログイン日時',
        'deleted_at' => '削除日時',
        'username' => 'ログインID',
        'gender' => '性別',
        'sex' => '性別',
        'birthday' => '生年月日',
        'postcode' => '郵便番号',
        'prefecture' => '都道府県',
        'address' => '所在地',
        'tel' => '電話番号',
        'fax' => 'FAX番号',
        'lat' => '緯度',
        'lng' => '経度',
        'location' => '位置情報',
        'description' => '詳細',
        'url' => 'URL',
        'qrcode' => 'QRコード',
        'image' => '画像',
        'images' => '画像',
        'title' => '件名',
        'content' => '内容',
        'comment' => 'コメント',

        'start_time' => '開始時間',
        'end_time' => '開始時間',
        'enabled' => '終日',

        'start_date' => '表示期間開始日',
        'end_date' => '表示期間終了日',

        // プロジェクト固有
        'sample' => 'サンプル',
        'participants' => '参加者',
        'is_complaint' => 'クレーム・トラブル',
        'client_type_name' => '会社種別',
        'client_type_icon' => '会社種別アイコン',
        'representative' => '代表者',
        'business_hours' => '営業時間',
        'drivers_count' => '運転者数',
        'languages' => '言語',
        'payment_method' => '支払い方法',
        'registration_number' => '旅行業登録番号',
        'scheduled_at' => '日時',
        'is_completed' => '対応済み',
        'contact_person' => '相手先担当者',
        'is_private' => '非公開',
        'client_id' => '会社',
        'department' => '所属部署',
        'img_file' => 'アイコン',
        'position' => '役職',
        'date_start' => '開始日',
        'date_end' => '終了日',
        'title_type' => 'スケジュール種別',

        'required_time' => '商談所要時間',

        // テーブル名
        'tables' => [
            'users' => 'メンバー',
            'admin_users' => '管理者',
            'clients' => '会社企業',
            'notices' => 'お知らせ',
            'reports' => '日報',
            'report_contents' => '報告',
            'report_comments' => 'コメント',
            'report_visitors' => '閲覧者',
            'report_visitors_count' => '閲覧者数',
            'report_content_likes' => 'いいね',
            'report_contents_sales' => '営業日報',
            'report_contents_work' => '業務日報',
            'report_content_product' => '商材評価',
            'meetings' => '議事録',
            'meeting_comments' => 'コメント',
            'meeting_visitors' => '閲覧者',
            'meeting_visitors_count' => '閲覧者数',
            'meeting_likes' => 'いいね',
            'products' => '商材',
            'genres' => 'ジャンル',
            'branches' => '営業所',
            'business_districts' => '営業エリア',
            'vehicles' => '保有車両',
            'contact_persons' => '相手先担当者',
            'office_todo_participants' => '社内担当者',
            'sales_todo_participants' => '社内担当者',
            'sales_methods' => '営業手段',
        ],

        // モデル固有のカスタムバリデーション属性名
        'admin_users' => [
            'username' => '管理者ID',
        ],
        'users' => [
            'username' => 'ログインID',
            'name' => 'メンバー名',
            'products' => '担当商材',
            'type_id' => '担当会社',
        ],
        'clients' => [
            'name' => '会社名',
            'name_kana' => '会社名よみがな',
        ],
        'client_type_taxibus' => [
            'client_id' => '会社ID',
            'membership_fee' => '会費',
            'fee_taxi_cab' => '手数料 タクシー CAB',
            'fee_taxi_tabinoashi' => '手数料 タクシー たびの足',
            'fee_bus_cab' => '手数料 バス CAB',
            'fee_bus_tabinoashi' => '手数料 バス たびの足',
            'category' => 'カテゴリー',
            'has_dr_sightseeing' => '観光DR',
            'has_dr_female' => '女性DR',
            'has_dr_language_english' => '外国語DR 英語',
            'has_dr_language_chinese' => '外国語DR 中国語',
            'has_dr_language_korean' => '外国語DR 韓国語',
            'has_dr_language_other' => '外国語DR 他言語',
            'has_wheelchair' => '車椅子',
            'has_baby_seat' => 'ベビーシート',
            'has_child_seat' => 'チャイルドシート',
            'fee_child_seat' => 'チャイルドシート料金',
            'has_junior_seat' => 'ジュニアシート',
            'fee_junior_seat' => 'ジュニアシート料金',
            'is_bus_association_member' => 'バス協加盟',
            'has_safety_mark' => 'セーフティーマーク',
        ],
        'client_type_travel' => [
            'payment_method' => '支払い方法',
            'registration_number' => '旅行業登録番号',
            'group' => 'JATA/ANTA/その他',
        ],
        'meetings' => [
            'started_at' => '開催日時',
            'title' => '会議名',
            'user_id' => '作成者ID',
            'content' => '議事内容',
        ],
        'report_contents_sales' => [
            'participants' => '面談者',
            'description' => '面談内容',
        ],
        'report_contents_work' => [
            'title' => '仕事内容',
            'description' => '本文',
        ],
        'sales_todos' => [
            'description' => '要件',
        ],
        'office_todos' => [
            'title' => 'タイトル',
            'description' => 'メモ',
        ],
    ]
];
