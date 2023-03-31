#!/bin/sh

REMOTE_BACKUP_PATH="~/grouptube-biz"

#echo "backup rsync [develop]"
#if [ -e ~/grouptube-biz/develop/current ]; then
##  echo "to local"
##  mkdir -p ~/backup/grouptube-biz/develop/images/
##  rsync -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/develop/shared/storage/app/images/ ~/backup/grouptube-biz/develop/images/
#
#  echo "to external"
#  mkdir -p ~/grouptube-biz/develop/shared/storage/app/images/
#  ssh -p 22 violetfox45@violetfox45.sakura.ne.jp "mkdir -p ${REMOTE_BACKUP_PATH}/dev/backup-images/"
#  rsync -e "ssh -p 22" -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/develop/shared/storage/app/images/ violetfox45@violetfox45.sakura.ne.jp:${REMOTE_BACKUP_PATH}/dev/backup-images/
#else
#  echo "skip"
#fi

#echo "backup rsync [staging]"
#if [ -e ~/grouptube-biz/staging/current ]; then
##  echo "to local"
##  mkdir -p ~/backup/grouptube-biz/staging/images/
##  rsync -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/staging/shared/storage/app/images/ ~/backup/grouptube-biz/staging/images/
#
#  echo "to external"
#  mkdir -p ~/grouptube-biz/staging/shared/storage/app/images/
#  ssh -p 22 violetfox45@violetfox45.sakura.ne.jp "mkdir -p ${REMOTE_BACKUP_PATH}/stg/backup-images/"
#  rsync -e "ssh -p 22" -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/staging/shared/storage/app/images/ violetfox45@violetfox45.sakura.ne.jp:${REMOTE_BACKUP_PATH}/stg/backup-images/
#else
#  echo "skip"
#fi

echo "backup rsync [production]"
if [ -e ~/grouptube-biz/production/current ]; then
#  echo "to local"
#  mkdir -p ~/backup/grouptube-biz/production/images/
#  rsync -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/production/shared/storage/app/images/ ~/backup/grouptube-biz/production/images/

  echo "to external"
  mkdir -p ~/grouptube-biz/production/shared/storage/app/images/
  ssh -p 22 violetfox45@violetfox45.sakura.ne.jp "mkdir -p ${REMOTE_BACKUP_PATH}/prod/backup-images/"
  rsync -e "ssh -p 22" -b --suffix=~$(date "+%Y%m%d%H%M%S") -azr ~/grouptube-biz/production/shared/storage/app/images/ violetfox45@violetfox45.sakura.ne.jp:${REMOTE_BACKUP_PATH}/prod/backup-images/
else
  echo "skip"
fi