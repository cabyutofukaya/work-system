# Laravel Management System

このプロジェクトは、Laravel 9 + PHP 8.3 をベースとした社内向けのWeb管理システムです。以下のような機能を備え、拡張性・保守性に優れた構成を目指しています。

---

## 🧰 使用技術スタック

- **Laravel Framework**: v9.x
- **PHP**: ^8.3
- **Inertia.js** + Vue.js フロントエンド（SPA構成対応）
- **Laravel-Admin**: 管理画面自動生成
- **Fortify / Sanctum**: 認証・APIセキュリティ
- **Telescope / Telescope Toolbar**: 開発用デバッグツール
- **Ziggy**: ルーティング情報のJS側への共有

---


## 📦 事前準備
①php8.3のインストール
②composerのインストール
③npmのインストール
④MAMP(XAMPP)のインストール


## Windows の場合
① PHP 8.3 のインストール
XAMPP for Windows(https://www.apachefriends.org/jp/index.html) をインストール（PHP8.3対応版）

または手動で PHP をインストール：
PHP公式サイト(https://windows.php.net/download/)
環境変数（PATH）に php.exe のあるフォルダを追加

インストール後、コマンドプロンプトで確認
php -v


② Composer のインストール
公式インストーラー(https://getcomposer.org/Composer-Setup.exe) を実行

インストール後、コマンドプロンプトで確認
composer -V


③ Node.js & npm のインストール
Node.js公式サイト(https://nodejs.org/ja) から LTS版をダウンロード＆インストール

インストール後、コマンドプロンプトで確認：
node -v 
npm -v


④-1,XAMPP のインストール
公式サイトからダウンロード
🔗 https://www.apachefriends.org/jp/index.html

ダウンロードしたインストーラー（例：xampp-windows-x64-8.3.0.exe）を実行

インストーラーを起動して、Apache と MySQL にチェックを入れてインストール

インストール後、XAMPP Control Panel を開く

「Apache」と「MySQL」を「Start」

④-2,MySQL データベースを作成する
ブラウザで以下にアクセス
　👉 http://localhost/phpmyadmin

左上の「新規作成」をクリック

任意のデータベース名を入力（例：work-system）

「作成」ボタンをクリック

※ XAMPP のデフォルト設定では、ユーザー名は root、パスワードは空です。



## 🍎 Mac の場合
① PHP 8.3 のインストール
Homebrew のインストール（未導入の場合）
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"


PHP 8.3 のインストール
brew install php@8.3
brew link php@8.3 --force

インストール後、コマンドプロンプトで確認：
php -v


② Composer のインストール
brew install composer

インストール後、コマンドプロンプトで確認：
composer -V

上記ができない場合
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer

インストール後、コマンドプロンプトで確認：
composer -V


③Node.js & npm のインストール
brew install node

インストール後、コマンドプロンプトで確認：
node -v
npm -v


④-1,MAMP のインストール
公式サイトからダウンロード
🔗 https://www.mamp.info/en/

「Free Version」を選び、MAMP.pkg をダウンロードしてインストール

MAMP アプリを起動し、「Start Servers」ボタンで Apache と MySQL を開始

④-2,
ブラウザで以下にアクセス：
　👉 http://localhost/phpMyAdmin

左側メニューの「新規作成」をクリック

任意のデータベース名を入力（例：work-system）

「作成」ボタンをクリック



## 📦 セットアップ手順

### 1. クローンと依存関係インストール

```bash
### ダウンロードしたファイルへ移動
cd work-system-main

### 必要なディレクトリを作成
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache
mkdir -p bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache


### 必要なライブラリをインストール
composer install
npm install && npm run dev

### 設定ファイルをコピー
cp .env_cp .env


### .envにて以下の値を確認
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889(※3306 XAMMPの場合)
DB_DATABASE=work_system(MAMPで作成したDB名と同じ)
DB_USERNAME=root
DB_PASSWORD=root

### XAMPP のデフォルト MySQL ポートは ※3306
### XAMPP のデフォルト設定では、ユーザー名は root、パスワードは空です。

### MAMP のデフォルト MySQL ポートは 8889
### MAMP  ユーザー名・パスワードはどちらも root

### laravelの設定ファイルのキャッシュ削除
php artisan config:cache

### DBのtable作成
php artisan migrate (yes)
### DBのシーディングを実行する
php artisan db:seed (yes)

### laravelサーバー立ち上げ
php artisan serve

###コマンドに表示されたURLを任意のブラウザに入力
http://127.0.0.1:8000


###ログイン情報
user01 / p@ssword


