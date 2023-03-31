#!/bin/bash -eux

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
#
# If you have user-specific configurations you would like
# to apply, you may also create user-customizations.sh,
# which will be run after this script.


# If you're not quite ready for the latest Node.js version,
# uncomment these lines to roll back to a previous version

# Remove current Node.js version:
#sudo apt-get -y purge nodejs
#sudo rm -rf /usr/lib/node_modules/npm/lib
#sudo rm -rf //etc/apt/sources.list.d/nodesource.list

# Install Node.js Version desired (i.e. v13)
# More info: https://github.com/nodesource/distributions/blob/master/README.md#debinstall
#curl -sL https://deb.nodesource.com/setup_13.x | sudo -E bash -
#sudo apt-get install -y nodejs

cd ~/code

# PHPバージョンの変更
source ~/.bash_aliases
php80

# パッケージを再インストール
rm -rf ~/code/vendor
composer config platform.php 8.0.2
composer install

# 環境設定ファイルを生成
if [[ ! -e .env ]]; then
  cp -p .env{.homestead.example,}
  php artisan key:generate
fi

# マイグレーション
php artisan migrate:refresh

# 会社・メンバーのダミーデータのシーディングを実行
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ClientSeeder

# その他のデータのダミーデータのシーディングを実行
php artisan db:seed

# ブラウザテスト用日本語フォント
sudo apt-get -y install fonts-ipafont

# staudenmeir/dusk-updaterによる環境に合わせたChromeDriverのアップデート
php artisan dusk:update --detect
