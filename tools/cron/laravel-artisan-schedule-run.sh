#!/bin/sh

PATH=/opt/remi/php80/root/usr/bin:/opt/remi/php80/root/usr/sbin:/usr/local/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/home/shin-gt/.local/bin:/home/shin-gt/bin:$PATH

echo "artisan schedule:run [develop]"
if [ -e ~/grouptube-biz/develop/current ]; then
  cd ~/grouptube-biz/develop/current && php artisan schedule:run &
else
  echo "skip"
fi

echo "artisan schedule:run [staging]"
if [ -e ~/grouptube-biz/staging/current ]; then
  cd ~/grouptube-biz/staging/current && php artisan schedule:run &
else
  echo "skip"
fi

echo "artisan schedule:run [production]"
if [ -e ~/grouptube-biz/production/current ]; then
  cd ~/grouptube-biz/production/current && php artisan schedule:run &
else
  echo "skip"
fi
