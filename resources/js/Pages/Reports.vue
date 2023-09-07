<style>
a.Link_item {
  text-decoration: none;
  color: rgba(0, 0, 0, 0.87);
  padding: 0px 20px;
}
</style>
<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-file-document</v-icon>
        日報
      </v-card-title>

      <v-card-text class="py-0">
        <ul>
          <li>最近更新されたものから順に表示されます。</li>
        </ul>
      </v-card-text>

      <v-card-text>
        <v-row>
          <v-col class="text-left">
            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined" :class="{ 'mb-2': $vuetify.breakpoint.xs }">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み<span v-if="formParamsCount">({{ formParamsCount }})</span>
            </Link>


            <Link as="Button" :to="report_url" :small="$vuetify.breakpoint.xs">
            <v-icon edit>
              mdi-reload
            </v-icon>
            リロード
            </Link>

            <!-- <a :href=report_url>
              <Link as="Button" :small="$vuetify.breakpoint.xs">
              <v-icon left>
                mdi-reload
              </v-icon>
              リロード
              </Link>
            </a> -->

          </v-col>



          <v-col class="text-right">
            <Link as="Button" :class="{ 'mb-2': $vuetify.breakpoint.xs }" :small="$vuetify.breakpoint.xs"
              :to="$route('reports.create')">
            <v-icon left>
              mdi-plus
            </v-icon>
            日報を作成する
            </Link>

            <Link as="Button" :to="$route('reports.mine')" :small="$vuetify.breakpoint.xs" class="ml-2">
            <v-icon edit>
              mdi-playlist-edit
            </v-icon>
            自分の日報を管理
            </Link>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4"></v-divider>

      <!-- <v-card-text class="py-0">

        <v-row>
          <v-col class="text-left">
            <v-switch dense filled class="ma-0 pa-0 py-0" color="warning" :error="Boolean(readed_form.errors.is_private)"
              :label="(readed_form.is_private) ? '未読' : ''"
              :prepend-icon="(readed_form.is_private) ? 'mdi-eye-off' : 'mdi-eye'" name="is_private"
              v-model="readed_form.is_private" :error-messages="readed_form.errors.is_private"
              @change="changed"></v-switch>
          </v-col>
        </v-row>
      </v-card-text> -->

      <v-list dense class="dense-list">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row class="font-weight-bold">
                <v-col cols="12" sm="2">
                  日付
                </v-col>
                <v-col cols="12" sm="2">
                  名前
                </v-col>
                <v-col cols="12" sm="6">
                  日報の種類
                </v-col>

                <v-col cols="12" sm="1" class="text-center">
                  いいね
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  コメント
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="(report,index) in reports['data']" :key="report.id" :id="'report_' + report.id">
          <!-- <Link as="v-list-item" :href="$route('reports.show', { id: report.id })" dusk="reportShow" class="Link_item"> -->
          <v-list-item class="Link_item">
            <v-list-item-content>
              <v-row :class="{ 'grey': report['is_visited'], 'lighten-4': report['is_visited'] }">
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2" :class="{ 'font-weight-bold': !report['is_visited'] }">
                    <!-- {{ report.date }} -->
                    {{ report.created_at.substr(0, 16) }}
                  </v-col>
                  <v-col sm="2" :class="{ 'font-weight-bold': !report['is_visited'] }">
                    {{ report.user.name }}
                  </v-col>
                  <v-col sm="6" :class="{ 'font-weight-bold': !report['is_visited'] }">
                    <a :href="$route('reports.show', { id: report.id })" dusk="reportShow">
                      <span v-if="report['report_contents_sales_exists']">営業日報</span><span
                        v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span
                        v-if="report['report_contents_work_exists']">業務日報</span>
                    </a>

    
                    <span v-if="report['is_zaitaku']">(在宅)</span>
                  </v-col>


                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1" :class="{ 'font-weight-bold': !report['is_visited'] }">
                      <!-- {{ report.date }} -->
                      {{ report.created_at.substr(0, 16) }}
                    </div>
                    <div class="mb-1" :class="{ 'font-weight-bold': !report['is_visited'] }">{{ report.user.name }}</div>
                    <div :class="{ 'font-weight-bold': !report['is_visited'] }">
                      <a :href="$route('reports.show', { id: report.id })" dusk="reportShow">
                        <span v-if="report['report_contents_sales_exists']">営業日報</span><span
                          v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span
                          v-if="report['report_contents_work_exists']">業務日報</span></a> 
                          <span v-if="report['is_zaitaku']">(在宅)</span>
                    </div>

                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ report["report_content_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center" :class="{ is_readed: report.is_readed != 0 }">
                    {{ report["report_comments_count"] }}
                  </v-col>


                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ report["report_content_likes_count"] }}
                    </v-chip>
                    <v-chip small class="mr-2" :class="{ is_readed: report.is_readed != 0 }">
                      コメント
                      {{ report["report_comments_count"] }}
                    </v-chip>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </v-list-item>
          <!-- </Link> -->

          <v-divider class="mx-4"></v-divider>
        </div>

        <div v-if="!reports['data'].length">
          <v-list-item>
            <v-list-item-content>
              <v-list-item-subtitle>
                条件に一致する日報は見つかりませんでした
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <v-card-text>
        <v-pagination v-model="page" :length="length" @input="changePage"></v-pagination>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 絞り込み条件ダイアログ -->
    <v-dialog v-model="searchDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="closeSearchDialog">
      <v-card flat tile>
        <v-card-title>
          絞り込み
        </v-card-title>

        <form @submit.prevent="search">
          <v-card-text>
            <v-list>
              <!-- 会社ページ「最近の営業日報」から遷移した場合のみ表示される会社ID使用スイッチ -->
              <v-list-item class="mb-4" v-if="client_id">
                <v-switch dense filled class="mt-0 ml-2" color="error" :label="client.name" hint="この会社の営業日報を含む日報のみを表示する"
                  persistent-hint name="client_id" v-model="form.client_id" :true-value="client_id" false-value=""
                  :error="Boolean(form.errors.client_id)" :error-messages="form.errors.client_id"></v-switch>
              </v-list-item>

              <v-list-item>
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="ワード検索"
                  hint="仕事内容・本文・会社名(ふりがな)・面談者・面談内容から指定したワードを含む項目に絞り込みます。YYYYMMDDを指定すると日付で検索できます" persistent-hint
                  name="word" v-model="form.word" maxlength="200" :error="Boolean(form.errors.word)"
                  :error-messages="form.errors.word" :autofocus="!$vuetify.breakpoint.xs"></v-text-field>
              </v-list-item>

              <v-list-item class="mt-4">
                <v-switch dense filled class="mt-0 ml-2" color="error" label="クレーム・トラブル" hint="クレーム・トラブルの報告に絞り込みます"
                  persistent-hint name="only_complaint" v-model="form.only_complaint" true-value="1" false-value=""
                  :error="Boolean(form.errors.only_complaint)" :error-messages="form.errors.only_complaint"></v-switch>
              </v-list-item>


              <v-list-item class="mt-4">
                <v-switch dense filled class="mt-0 ml-2" color="warning" label="未読分表示" hint="未読分を絞り込みます" persistent-hint
                  name="is_visited" v-model="form.is_visited" true-value="1" false-value=""
                  :error="Boolean(form.errors.is_visited)" :error-messages="form.errors.is_visited"></v-switch>
              </v-list-item>


              <v-list-item class="mt-4">
                <v-switch dense filled class="mt-0 ml-2" color="warning" label="コメント未読あり" hint="コメント未読あり" persistent-hint
                  name="is_readed" v-model="form.is_readed" true-value="1" false-value=""
                  :error="Boolean(form.errors.is_readed)" :error-messages="form.errors.is_readed"></v-switch>
              </v-list-item>


              <v-list-item class="mt-4"
                v-if="user.admin_report == 1">
                <v-switch dense filled class="mt-0 ml-2" color="blue" label="在宅" persistent-hint name="is_zaitaku"
                  v-model="form.is_zaitaku" true-value="1" false-value="" :error="Boolean(form.errors.is_zaitaku)"
                  :error-messages="form.errors.is_zaitaku"></v-switch>
              </v-list-item>


              <v-list-item class="mt-4">
                <v-select dense filled clearable multiple label="所属部署" hint="表示する所属部署を選択してください" persistent-hint
                      :items="department_list" name="department"
                      v-model="form.department">
                    </v-select>

              </v-list-item>


              <v-list-item class="mt-4"
              v-if="user.admin_report == 1">
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="開始表示期間" class="mx-5"
                  hint="表示を絞り込む開始期間を記載してください" persistent-hint type="date" name="start_date" v-model="form.start_date"
                  maxlength="200" :error="Boolean(form.errors.start_date)" :error-messages="form.errors.start_date"
                  :autofocus="!$vuetify.breakpoint.xs"></v-text-field>

                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="終了表示期間" class="mx-5"
                  hint="表示を絞り込む終了期間を記載してください" persistent-hint type="date" name="end_date" v-model="form.end_date"
                  maxlength="200" :error="Boolean(form.errors.end_date)" :error-messages="form.errors.end_date"
                  :autofocus="!$vuetify.breakpoint.xs"></v-text-field>
              </v-list-item>


            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Button :small="$vuetify.breakpoint.xs" type="submit">
              <v-icon left>
                mdi-magnify
              </v-icon>
              この条件で検索する
            </Button>
          </v-card-text>
        </form>

        <v-divider></v-divider>


        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>
  </Layout>
</template>

<script src="https://unpkg.com/axios/dist/axios.min.js"/>

<style scoped>
/* 既読時の背景色とボーダーの間にスペースができるためネガティブマージンをデフォルトの-12から減らす */
.v-list-item .row {
  margin-top: -8px;
  margin-bottom: -8px;
}

.is_readed {
  color: rgb(240, 49, 42);
}
</style>

<!-- <script>
window.addEventListener('pageshow', () => {

  var perfEntries = performance.getEntriesByType("navigation");
  console.log(perfEntries);

});

</script> -->



<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";



export default {
  components: { Layout, Link },

  props: ['reports', 'form_params', 'client', 'user', 'report_url','is_phone'],

  
 

  data() {

    console.log(this.reports['data']);

    return {
      // ページ情報の初期値
      page: Number(this.reports['current_page']),
      length: Number(this.reports['last_page']),

      // 検索パラメータの中の会社IDの初期値
      client_id: this.$route().params["client_id"] ?? null,

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(this.form_params),

      // 設定されている検索条件の件数
      formParamsCount: Object
        .entries(this.form_params)
        .filter(function (param) {
          return param[1] && param[1].length !== 0;
        }).length,

      count: 0,
      department_list: ['CCD', 'TCD', 'ICTS', '経理', 'WSD', 'MGT','BPD'],


    };
  },

  mounted(){

    this.$nextTick(function() {
      // nextTickを使用してコンソールにログを出力します。
      setTimeout(function(){
        window.scrollBy(0, -100);
        console.log('uuuu');
      },500);
     
     
     
    });

    // window.moveTo(0,-200);

   
    // this.$nextTick(() => {

    //   let entries = performance.getEntriesByType("navigation");
    
    //   if (sessionStorage.getItem("scrollY") != null) {
    //     alert(sessionStorage.getItem("scrollY"));
    //       window.scrollTo(0, sessionStorage.getItem("scrollY"));
    //   }
    // })();
    
    // window.addEventListener("beforeunload", () => {
    //   sessionStorage.setItem("scrollY", window.scrollY);
    // });

    
    // window.onload = ()=>{
    //     // alert('ページが読み込まれました！')
    //     // window.moveBy(0,-1000);

    //   // window.scrollBy(0, window.innerHeight);
    //   window.setTimeout(alert(), 50000);
      
    //   // const scrollY = window.scrollY || window.pageYOffset
    //   // window.scrollTo({
    //   //   top: scrollY - 1000,
    //   //   behavior: 'smooth'
    //   // });
    //   alert('ページが読み込まれました！');
    // }
    // console.log(this.reports);
    // console.log(this.report_url);

    // console.log(this.is_phone);

    // if(this.is_phone){
    //   alert();
    //   var url = '/api' + this.report_url;
    //     axios
    //     .get(url)
    //     .then((response) => {
    //     // handle success(axiosの処理が成功した場合に処理させたいことを記述)
    //     console.log(response.data);

    //     console.log(response.data['current_page']);
    //     console.log(response.data.current_page);
    //     console.log(response.data.data);
    //     console.log(this.reports.data);

    //     this.reports.data = response.data.data;

    //     })
    //     .catch((error) => {
    //     // handle error(axiosの処理にエラーが発生した場合に処理させたいことを記述)
    //     console.log(error);
    //     })
    //     .finally( () =>  {
    //     // always executed(axiosの処理結果によらずいつも実行させたい処理を記述)
    //     console.log('終了');
    //     });
    // }
   

  },


  //   mounted() {
  //     window.onpopstate = function (event) {
  //       alert();
  //       console.log(event);
  //       console.log("popstate action");
  //     };
  // },
  // mounted() {

  //   // this.$router.go({ path: this.$router.currentRoute.path, force: true });


  //   history.pushState(null, null, null);
  //   window.addEventListener('pageshow', () => {


  //     var perfEntries = performance.getEntriesByType("navigation");
  //     // let url = '/meetings';
  //     var url_type = 0;

  //     perfEntries.forEach(function (pe) {
  //       switch (pe.type) {
  //         case 'navigate':
  //           console.log('通常のアクセス');
  //           break;
  //         case 'reload':
  //           // url_type = 1;
  //           console.log('更新によるアクセス');
  //           break;
  //         case 'back_forward':
  //           url_type = 1;
  //           console.log('戻るによるアクセス');
  //           break;
  //         case 'prerender':
  //           console.log('レンダリング前');
  //           break;
  //       }
  //     });

  //     if(url_type == 1){
  //       window.location.href = this.report_url;
  //       this.$inertia.get(this.report_url);
  //     }


  //     alert(1);
  //     console.log(performance.getEntriesByType("navigation"));
  //     console.log(performance.getEntriesByType("navigation")[0].type);

  //     if (performance.getEntriesByType("navigation")[0].type === 'back_forward') {
  //       //ここに処理
  //       alert(2);
  //       let url = '/reports';
  //       this.$inertia.get(url);
  //       // this.$router.go({ path: this.$router.currentRoute.path, force: true });
  //       alert(3);

  //     }
  //   });





  //   window.addEventListener('popstate', () => {
  //     alert(66);
  //   });

  //   history.replaceState(null, null, null);
  //   window.addEventListener('popstate', this.handlePopstate);
  //   history.replaceState(null, null, null);
  //   alert("popstate");
  //   window.addEventListener("popstate", this.handlePopstate);
  // },
  // beforeDestroy() {
  //   window.removeEventListener("popstate", this.handlePopstate);
  // },

  // beforeRouteLeave(to, from, next) {
  //   alert(1);
  //   const answer = window.confirm('保存していないデータがありますが、ページを離れてもよろしいですか？');
  // },

  // popstateEvent() {
  //   window.confirm('保存していないデータがありますが、ページを離れてもよろしいですか？');
  // },

  methods: {
    // ページ移動

    changePage() {
      // サーバ側で生成された検索パラメータを含む最終ページURLを取得
      let url = new URL(this.reports["last_page_url"]);

      // ページ数の書き換え
      if (this.page !== 1) {
        url.searchParams.set('page', String(this.page));
      } else {
        url.searchParams.delete('page');
      }

      // ページ移動
      this.$inertia.get(url.href);
    },

    forceUpdateMyComponent() {
      this.$forceUpdate()
    },

    // 絞り込み実行
    search() {
      this.form
        .transform((data) => {
          // 値が空の要素を削除
          Object.entries(data).forEach((kv) => {
            console.log(kv);
            if (kv[1] === "" || kv[1] === null) {
              delete data[kv[0]];
            }
          });

          return { ...data };
        })
        .get(this.$route('reports.index'), {
          onSuccess: () => {
            this.closeSearchDialog();
          },
        });
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;
    },

    reload(count) {
      alert(count);
    },

    // handlePopstate() {
    //   alert('3');
    //   console.log("popstate action");
    // },

  }
}
</script>
