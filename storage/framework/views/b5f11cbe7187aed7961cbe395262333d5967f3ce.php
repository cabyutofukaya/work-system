<div class="box box-default" style="padding:10px">
    左のメニューより利用したい項目を選んでください。
</div>

<div class="box box-default" style="padding:10px">
    <?php if(Auth::guard('admin')->user()->roles->contains('slug', 'administrator')): ?>
        <h4>管理ツール</h4>
        <ul>
            <li>
                <a href="<?php echo e(url("telescope"), false); ?>" target="_blank">Laravel Telescope</a>
            </li>
        </ul>
    <?php endif; ?>

    <?php if(Auth::guard('admin')->user()->roles->contains('slug', 'administrator') || Auth::guard('admin')->user()->roles->contains('slug', 'adminuser')): ?>
        <h4>リンク</h4>
        <ul>
            <li>
                <a href="<?php echo e(route("home"), false); ?>" target="_blank">メンバーページ</a>
            </li>
        </ul>

        <h4>バックアップ仕様</h4>
        <ul>
            <li>
                毎日<?php echo e(env('BACKUP_RUN_DAILY_AT', '03:00'), false); ?>頃にデータベースと画像のバックアップが行われます。<br>
                バックアップファイルは各サーバにSFTPで接続することでダウンロードできます。
            </li>

            <li>
                データベースのバックアップはウェブサーバ内に1日1ファイル(YYYY-MM-DD-HH-mm_ss.zip)生成されます。<br>
                古いバックアップは自動で削除されません。<br>
                SFTPホスト名：grouptube.biz<br>
                ログインID：shin-gt<br>
                バックアップ先：grouptube-biz/<?php echo e(config("app.env"), false); ?>/shared/storage/app/backup
            </li>

            <li>
                データベースのバックアップファイルは外部バックアップサーバにも保存されます。<br>
                SFTPホスト名：<?php echo e(config("filesystems.disks.sftp.host"), false); ?><br>
                ポート：<?php echo e(config("filesystems.disks.sftp.port"), false); ?><br>
                ログインID：<?php echo e(config("filesystems.disks.sftp.username"), false); ?><br>
                バックアップ先：<?php echo e(config("filesystems.disks.sftp.root"), false); ?>/backup<br>
            </li>

            <li>
                アップロードされた会社や保有車両の画像ファイルはウェブサーバ内の以下ディレクトリに保存されます。<br>
                grouptube-biz/<?php echo e(config("app.env"), false); ?>/shared/storage/app/images<br>
                画像を差し替えたり、会社や保有車両を削除してもサーバ上からは画像は削除されません。
            </li>

            <li>
                画像ファイルは外部バックアップサーバ <?php echo e(config("filesystems.disks.sftp.host"), false); ?> にもバックアップされます。<br>
                バックアップ先：<?php echo e(config("filesystems.disks.sftp.root"), false); ?>/backup-images
            </li>
        </ul>

        <h4>バックアップの動作確認・ローカル保存</h4>
        <ul>
            <li>
                定期的にバックアップファイルをサーバからダウンロードしてローカルに保存してください。<br>
                またウェブサーバ・外部バックアップサーバ共にバックアップが正しく行われているか以下の手順で確認してください。
            </li>
            <li>
                データベースのバックアップではzipファイルに含まれるSQLダンプファイルに最新に日報やコメントが含まれるかどうか確認してください。<br>
                また以前のバックアップより新しいバックアップのファイルサイズが小さくなってなっていないことを確認してください。<br>
                データベースの使用量は増えていきますので、通常はバックアップごとにファイルサイズは大きくなっていきます。
            </li>
            <li>
                画像ファイルのバックアップでは最近投稿された画像が含まれるかどう確認してください。<br>
                また以前のバックアップよりファイル数が減っていないことを確認してください。<br>
                画像ファイルは削除されませんので、ファイル数は増えていきます。
            </li>
        </ul>

        <h4>ストレージ使用量の確認</h4>
        <ul>
            <li>
                定期的にサーバのストレージ使用量を確認してください。
            </li>
            <li>
                ウェブサーバの使用量はさくらインターネットのサーバコントロールパネルから確認し、使用量が総量の80%を超えないようにしてください。<br>
                <a href="https://secure.sakura.ad.jp/rs/cp/" target="_blank">https://secure.sakura.ad.jp/rs/cp/</a><br>
                ログインID(ドメイン名): grouptube.biz<br>
                ホーム > ディスク使用量
            </li>
            <li>
                外部バックアップサーバの使用量はさくらインターネットのサーバコントロールパネルから確認し、使用量が総量の80%を超えないようにしてください。<br>
                <a href="https://secure.sakura.ad.jp/rs/cp/" target="_blank">https://secure.sakura.ad.jp/rs/cp/</a><br>
                ログインID(ドメイン名): <?php echo e(config("filesystems.disks.sftp.host"), false); ?><br>
                ホーム > ディスク使用量
            </li>
        </ul>

        <h4>GoogleMap利用料金の確認</h4>
        <ul>
            <li>
                本システムでは地図の表示に Google Cloud Platform の Maps JavaScript API, Geocoding API を利用しています。<br>
                Maps JavaScript API は地図を表示するごとに1回、Geocoding API は住所検索を行うごとに1回利用されます。
            </li>
            <li>
                各APIには使用回数に応じた料金が発生し、毎月の無料枠 $200 の超過分が登録されたクレジットカードから自動的に支払われます。<br>
                APIの使用回数や見込み課金額は <a href="https://console.cloud.google.com" target="_blank">https://console.cloud.google.com</a> から確認できます。
            </li>
            <li>
                各APIの1000回あたりの料金は以下のとおりです。<br>
                Maps JavaScript API : $7,  Geocoding API : $5
            </li>
            <li>
                各金額の情報は2022年2月現在のものとなります。最新の情報は Google Cloud Platform で確認してください。
            </li>
        </ul>
    <?php endif; ?>
</div><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/resources/views/admin/home.blade.php ENDPATH**/ ?>