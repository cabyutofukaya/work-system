GROUPTUBE
================================================================================

## 概要

### URL

各環境ともにHTTP認証あり    
id:grouptube pw:********

#### 本番環境

* 公開URL  
  https://grouptube.biz
* 管理画面URL  
  https://grouptube.biz/admin
* id:admin pw:********

#### ステージング環境

* 会員URL  
  https://stg.grouptube.biz  
  テストユーザ id:user pw:secret
* 管理画面URL  
  https://stg.grouptube.biz/admin  
  id:admin pw:********

#### 開発プレビュー環境

* 会員URL  
  https://dev.grouptube.biz/   
  テストユーザ id:user pw:secret
* 管理画面URL  
  https://dev.grouptube.biz/admin  
  id:admin pw:********

### フロントサイド開発

Homestead/Windows環境ではゲスト側でnpmが動作しないためホスト側でLaravel-Mixによる開発を行う。
開発中はwatchタスクよりファイルの変更を監視しアセットコンパイルを行う。

    $ npm install
    $ npm run watch

### サーバサイド開発

#### テスト

機能(Feature)テスト (未実装)

    $ phpunit --stop-on-error --stop-on-failure --debug  

ブラウザテスト

    $ artisan dusk:update --detect
    $ artisan dusk --stop-on-error --stop-on-failure --verbose --debug

### デプロイ

以下リモートブランチにプッシュするとCI/CDが実行されデプロイされる。

| ブランチ            | 環境        |
|-----------------|--------- --|
| master          | 本番環境        |
| release-staging | ステージング環境  |
| release-develop | 開発プレビュー環境 |

手動でのデプロイ実行はローカル開発環境からコマンドを実行する。

    $ vendor/bin/dep deploy develop
    $ vendor/bin/dep deploy staging
    $ vendor/bin/dep deploy production

---------------------------------------------------------------------------------

## セットアップ

### サーバ

#### プラットフォーム

さくらのレンタルサーバ

コントロールパネル  
https://secure.sakura.ad.jp/rs/cp/  
ID: shin-gt.sakura.ne.jp  
PW: (設定されたパスワード)

#### ドメイン/SSL設定

ドメイン/SSL > ドメイン/SSL

    ドメイン新規作成
    -> さくらインターネットで取得の独自ドメインを使う
    -> ドメイン一覧から [grouptube.biz] 選択
    -> ☑ サブドメインを指定する {dev,stg}
    -> 追加
    -> 追加されたドメインの「設定」ボタン
    -> [チェック外す] www.が付与されたサブドメインも利用する
    -> Web公開フォルダ /{dev,stg,prod}
    -> www.転送設定 ☑ {,stg.,dev.}grouptube.biz に転送する
    -> このドメイン宛のメールは全てユーザに受信させる
    -> SPFレコードの使用 ☑ 利用する

SSLの設定

    リストより各ドメインの [SSL] ボタン
    -> ☑ HTTPSに転送する

HTTPS転送設定

    リストより各ドメインの [設定] ボタン
    -> SSL証明書の利用種類を選択
    -> Let's Encrypt (無料SSL)
    -> [利用する]ボタン

#### PHPメモリ割り当て

スクリプト設定 > php.ini設定

    upload_max_filesize = 128M
    post_max_size = 128M
    memory_limit = 1024M

#### データベース作成設定

Webサイト/データ > データベース

    起動
    新規追加
    -> データベース名
        grouptube (本番環境用)
        grouptube_stg (ステージング環境用)
        grouptube_dev (開発環境用)

#### アクセスログ

サーバステータス > アクセスログ ＞ アクセスログ設定

    アクセスログの保存
        ☑ 保存する
        ☑ エラーログも保存する
    アクセスログの保存期間
        24ヶ月分
    ホスト名の情報
        ☑ 保存する

#### シェルからの各種設定

sshログインして作業

デフォルトシェルを変更

    $ chsh -s /usr/local/bin/bash
    $ cp ~/.{,ba}shrc
    $ cat <<'EOL' >> ~/.bash_profile
    if [ -f $HOME/.bashrc ]; then
    source $HOME/.bashrc
    fi
    EOL

.inputrc を設定

    $ cat <<'EOL' >> ~/.inputrc
    "\e[1~": beginning-of-line
    "\e[2~": overwrite-mode
    "\e[3~": delete-char
    "\e[4~": end-of-line
    EOL

composer インストール

    $ curl -sS https://getcomposer.org/installer | php
    $ ln -sv ~/composer.phar ~/composer

ドメインのルートディレクトリを作成

    $ mkdir ~/www/grouptube.biz

各環境にHTTP認証を設定  

    コントロールパネル
    -> Webサイト/データ
    -> ファイルマネージャー
    -> /grouptube.biz
    -> 表示アドレスへの操作
    -> アクセス設定
    -> パスワード制限
        ☑ パスワード制限を使用する
        ☑ 両方の許可がないとアクセス不能
        パスワードファイル : /.htpasswd
    -> パスワードファイルの編集(初回のみ)
        ユーザ名 : grouptube
        パスワード : *******
        コメント : 全環境アクセス認証
    -> OK

---------------------------------------------------------------------------------

## コードリポジトリ

GitHub非公開リポジトリ  
https://github.com/cellnavicorp/grouptube-biz

### ブランチ構成

- master ブランチ  
  このブランチへのプッシュは速やかに本番環境にデプロイされる

- release-staging ブランチ  
  このブランチへのプッシュは速やかにステージング環境にデプロイされる

- release-develop ブランチ  
  このブランチへのプッシュは速やかにステージング環境にデプロイされる データベース内容は維持されず、開発都合により変更される

---------------------------------------------------------------------------------

## デプロイ

### 本番サーバへ接続するためのssh設定

sshキーペアの生成

    $ ssh-keygen -t rsa -f ~/.ssh/id_rsa.prod -C "grouptube.biz"
    (パスフレーズを設定)

本番サーバに公開鍵を設置

    $ ssh-copy-id -i ~/.ssh/id_rsa.prod shin-gt@grouptube.biz

接続設定

    $ cat <<'EOL' >> ~/.ssh/config
    Host grouptube.biz
        User shin-gt
        Port 22
        Hostname grouptube.biz
        IdentityFile ~/.ssh/id_rsa.prod
        TCPKeepAlive yes
        IdentitiesOnly yes
    EOL
    $ chmod 600 ~/.ssh/config

接続をテスト

    $ ssh grouptube.biz

### リモートリポジトリに接続するためのssh設定

公開鍵を登録  
https://github.com/cellnavicorp/grouptube-biz/settings/keys/new

    SSH 鍵を追加
    - Label : grouptube.biz
    - Key : (~/.ssh/id_rsa.prod.pub の内容) 

接続設定

    $ cat <<'EOL' >> ~/.ssh/config
    Host github.com
        User git
        Port 22
        Hostname github.com
        IdentityFile ~/.ssh/id_rsa.prod
        TCPKeepAlive yes
        IdentitiesOnly yes
    EOL
    $ chmod 600 ~/.ssh/config

接続テスト

    $ ssh -T git@github.com
    Hi cellnavicorp/grouptube-biz! You've successfully authenticated, but GitHub does not provide shell access.

### ssh-agent設定

ローカル開発環境へのログイン時に自動でパスワードを確認する。

    $ cat <<'EOL' >> ~/.ssh-agent.sh
    # Setup ssh-agent
    if [ -f ~/.ssh-agent ]; then
        . ~/.ssh-agent > /dev/null
    fi
    if [ -z "$SSH_AGENT_PID" ] || ! kill -0 $SSH_AGENT_PID; then
        ssh-agent > ~/.ssh-agent
        . ~/.ssh-agent > /dev/null
    fi
    ssh-add -l >& /dev/null || ssh-add ~/.ssh/id_rsa.prod > /dev/null
    EOL
    $ cat ~/.ssh-agent.sh >> ~/.bashrc

接続テスト

    $ ssh grouptube.biz
    $ ssh -T git@github.com

### Deployerによるデプロイ

本番サーバからGitHubへのssh接続時に行われるフィンガープリント確認を省略

    $ ssh grouptube.biz
    $ cat <<'EOL' >> ~/.ssh/config
    Host github.com
        StrictHostKeyChecking no
    EOL
    $ chmod 600 ~/.ssh/config
    $ exit

初回デプロイ実行  
空の.envが生成されるためDBに接続できずmigrateはスキップされる

    $ cd ~/code
    $ vendor/bin/dep deploy

テンプレートをコピーして.envファイルを生成しパスワード等を設定

    $ vendor/bin/dep ssh
    $ cp .env{.develop.example|.staging.example|.production.example,}
    $ vi .env
    $ php artisan key:generate
    $ exit

再度デプロイ実行

    $ cd ~/code
    $ vendor/bin/dep deploy

ステージング・本番環境であれば会社・メンバーデータのインポートを実行

    # CSVファイルを転送 確認
    $ rsync -avzr --delete ~/code/storage/import grouptube.biz:~/grouptube-biz/{staging,production}/shared/storage/ -n
    # CSVファイルを転送
    $ rsync -avzr --delete ~/code/storage/import grouptube.biz:~/grouptube-biz/{staging,production}/shared/storage/
    # インポート
    $ vendor/bin/dep ssh
    $ php -d memory_limit=-1 artisan command:import-clients
    $ php -d memory_limit=-1 artisan command:import-users
    # パスワードが含まれるためCSVファイルを削除
    $ find ~/grouptube-biz/{staging,production}/shared/storage/import/ -name "*.csv" -not -name "*.sample.csv"
    $ find ~/grouptube-biz/{staging,production}/shared/storage/import/ -name "*.csv" -not -name "*.sample.csv" --delete

開発環境であれば会社・メンバーのダミーデータのシーディングを実行

    $ vendor/bin/dep ssh devlop
    $ php -d memory_limit=-1 artisan db:seed --class=UserSeeder
    $ php -d memory_limit=-1 artisan db:seed --class=ClientSeeder

開発・ステージング環境であればダミーデータのシーディングを実行

    $ vendor/bin/dep ssh
    $ php -d memory_limit=-1 artisan db:seed

管理ユーザのパスワードを設定

    $ vendor/bin/dep ssh
    $ php artisan tinker
    >>>  config('admin.database.users_model')::where('username', 'developer')->update(['password' => \Hash::make('******')])
    >>>  config('admin.database.users_model')::where('username', 'admin')->update(['password' => \Hash::make('******')])
    $ exit

開発・ステージング環境であればテストユーザのパスワードを設定

    $ vendor/bin/dep ssh
    $ php artisan tinker
    >>>  App\Models\User::where('username', 'user')->update(['password' => \Hash::make('******')])
    $ exit

公開パスにシンボリックリンク

    $ vendor/bin/dep ssh
    $ ln -fnsv ~/grouptube-biz/production/current/public ~/www/grouptube.biz/prod
    $ ln -fnsv ~/grouptube-biz/staging/current/public ~/www/grouptube.biz/stg
    $ ln -fnsv ~/grouptube-biz/develop/current/public ~/www/grouptube.biz/dev

動作を確認

https://grouptube.biz  
https://stg.grouptube.biz  
https://dev.grouptube.biz

アセットコンパイルファイルをコミットしていないためエラー表示となる  
アセットファイルはGitHub Actionsでの自動デプロイ中で生成される

---------------------------------------------------------------------------------

## CI/CD

### GitHub Actions によるテスト・デプロイ自動化

ローカル環境に設定されているSSH鍵をGitHubに登録

https://github.com/cellnavicorp/grouptube-biz/settings/secrets

本番サーバへの接続用 SSH_KEY

    パスフレーズを削除した鍵を取得
    $ openssl rsa -in ~/.ssh/id_rsa.prod -out ~/.ssh/id_rsa.prod.nopw

    opensshフォーマットの秘密鍵だった場合はPEM形式に変換してパスフレーズを削除
    $ cp -p ~/.ssh/id_rsa.prod{,.nopw}
    $ ssh-keygen -p -m pem -f ~/.ssh/id_rsa.prod.nopw
    
    $ cat ~/.ssh/id_rsa.prod.nopw
    $ shred --remove ~/.ssh/id_rsa.prod.nopw

本番サーバへの接続用 KNOWN_HOSTS

    $ ssh-keyscan -H grouptube.biz 2>/dev/null

本番環境ブランチ master  
ステージング環境ブランチ release-staging  
開発プレビュー環境ブランチ release-develop  
へそれぞれコミットをプッシュすることで GitHub Actions によりテストとデプロイが自動で行われる。

---------------------------------------------------------------------------------

## タスクスケジュール

各環境のスケジュールをまとめて実行するスクリプトを設置

    $ mkdir ~/bin
    $ vi ~/bin/laravel-artisan-schedule-run.sh
    $ chmod 700 ~/bin/laravel-artisan-schedule-run.sh

レンタルサーバのコントロールパネルからスクリプトをcronに登録

スクリプト設定 > CRON設定 > スケジュール追加  
2分ごとの起動が最短の設定となる。

    コマンド : /home/shin-gt/bin/laravel-artisan-schedule-run.sh >> /dev/null 2>&1
    コメント : grouptube-biz Laravel Scheduling
    月 / 日 / 時 : すべて *
    分 :  */2
    曜日 : すべてチェック

エラー時のメッセージはpostmaster@ドメインに届きWebメールから確認できる。

https://secure.sakura.ad.jp/rscontrol/ms/webmail2/?mbox=postmaster

---------------------------------------------------------------------------------

### ストレージバックアップ

アップロード画像のバックアップをrsyncで行う  
laravel-backupでのバックアップでは容量が大きいzip生成に失敗するため

本番サーバにログイン

    $ vendor/bin/dep ssh

sshキーペアの生成

    $ ssh-keygen -t rsa -C "grouptube.biz"
    (空のパスフレーズを設定)

バックアップ先の外部サーバに公開鍵を設置

    $ ssh-copy-id -i ~/.ssh/id_rsa violetfox45@violetfox45.sakura.ne.jp

バックアップスクリプトを設置

    $ rsync -v  ~/grouptube-biz/production/current/tools/cron/laravel-backup-rsync.sh ~/bin/
    $ chmod 700 ~/bin/laravel-backup-rsync.sh

レンタルサーバのコントロールパネルからスクリプトをcronに登録

スクリプト設定 > CRON設定 > スケジュール追加  
15分毎にバックアップを行うよう設定

    コマンド : /home/shin-gt/bin/laravel-backup-rsync.sh >> /dev/null 2>&1
    コメント : grouptube-biz Storage Backup Rsync
    月 / 日 / 分 : すべて *
    時 :  15
    曜日 : すべてチェック

エラー時のメッセージはpostmaster@ドメインに届きWebメールから確認できる。

https://secure.sakura.ad.jp/rscontrol/ms/webmail2/?mbox=postmaster

-------------------------------------------------------------------------------