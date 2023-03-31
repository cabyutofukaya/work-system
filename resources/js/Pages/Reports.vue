<template>
  <Layout>
    <v-card
        flat tile
    >
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
          <v-col>
            <Link
                as="Button"
                :small="$vuetify.breakpoint.xs"
                @click.native="searchDialog = true"
                :color="formParamsCount ? 'warning' : undefined"
            >
              <v-icon left>
                mdi-filter-outline
              </v-icon>
              絞り込み<span v-if="formParamsCount">({{ formParamsCount }})</span>
            </Link>
          </v-col>

          <v-col class="text-right">
            <Link as="Button"
                  :class="{'mb-2' : $vuetify.breakpoint.xs}"
                  :small="$vuetify.breakpoint.xs"
                  :to="$route('reports.create')"
            >
              <v-icon left>
                mdi-plus
              </v-icon>
              日報を作成する
            </Link>

            <Link as="Button"
                  :to="$route('reports.mine')"
                  :small="$vuetify.breakpoint.xs"
                  class="ml-2"
            >
              <v-icon edit>
                mdi-playlist-edit
              </v-icon>
              自分の日報を管理
            </Link>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4"></v-divider>

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

        <div v-for="report in reports['data']" :key="report.id">
          <Link as="v-list-item" :href="$route('reports.show', {id: report.id})" dusk="reportShow">
            <v-list-item-content>
              <v-row :class="{'grey': report['is_visited'], 'lighten-4': report['is_visited']}">
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2" :class="{'font-weight-bold': !report['is_visited']}">
                    {{ report.date }}
                  </v-col>
                  <v-col sm="2" :class="{'font-weight-bold': !report['is_visited']}">
                    {{ report.user.name }}
                  </v-col>
                  <v-col sm="6" :class="{'font-weight-bold': !report['is_visited']}">
                    <span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1" :class="{'font-weight-bold': !report['is_visited']}">{{ report.date }}</div>
                    <div class="mb-1" :class="{'font-weight-bold': !report['is_visited']}">{{ report.user.name }}</div>
                    <div :class="{'font-weight-bold': !report['is_visited']}"><span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span></div>
                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ report["report_content_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center">
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
                    <v-chip small class="mr-2">
                      コメント
                      {{ report["report_comments_count"] }}
                    </v-chip>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </Link>

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
        <v-pagination
            v-model="page"
            :length="length"
            @input="changePage"
        ></v-pagination>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 絞り込み条件ダイアログ -->
    <v-dialog
        v-model="searchDialog"
        :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
        @click:outside="closeSearchDialog"
    >
      <v-card flat tile>
        <v-card-title>
          絞り込み
        </v-card-title>

        <form @submit.prevent="search">
          <v-card-text>
            <v-list>
              <!-- 会社ページ「最近の営業日報」から遷移した場合のみ表示される会社ID使用スイッチ -->
              <v-list-item class="mb-4" v-if="client_id">
                <v-switch
                    dense filled
                    class="mt-0 ml-2"
                    color="error"
                    :label="client.name"
                    hint="この会社の営業日報を含む日報のみを表示する" persistent-hint
                    name="client_id"
                    v-model="form.client_id"
                    :true-value="client_id"
                    false-value=""
                    :error="Boolean(form.errors.client_id)"
                    :error-messages="form.errors.client_id"
                ></v-switch>
              </v-list-item>

              <v-list-item>
                <v-text-field
                    dense filled clearable
                    prepend-inner-icon="mdi-pencil"
                    label="ワード検索"
                    hint="仕事内容・本文・会社名(ふりがな)・面談者・面談内容から指定したワードを含む項目に絞り込みます。YYYYMMDDを指定すると日付で検索できます" persistent-hint
                    name="word"
                    v-model="form.word"
                    maxlength="200"
                    :error="Boolean(form.errors.word)"
                    :error-messages="form.errors.word"
                    :autofocus="!$vuetify.breakpoint.xs"
                ></v-text-field>
              </v-list-item>

              <v-list-item class="mt-4">
                <v-switch
                    dense filled
                    class="mt-0 ml-2"
                    color="error"
                    label="クレーム・トラブル"
                    hint="クレーム・トラブルの報告に絞り込みます" persistent-hint
                    name="only_complaint"
                    v-model="form.only_complaint"
                    true-value="1"
                    false-value=""
                    :error="Boolean(form.errors.only_complaint)"
                    :error-messages="form.errors.only_complaint"
                ></v-switch>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Button
                :small="$vuetify.breakpoint.xs"
                type="submit"
            >
              <v-icon left>
                mdi-magnify
              </v-icon>
              この条件で検索する
            </Button>
          </v-card-text>
        </form>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button
              class="mt-4"
              :small="$vuetify.breakpoint.xs"
              @click.native="searchDialog = false"
          >
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

<style scoped>
/* 既読時の背景色とボーダーの間にスペースができるためネガティブマージンをデフォルトの-12から減らす */
.v-list-item .row {
  margin-top: -8px;
  margin-bottom: -8px;
}
</style>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['reports', 'form_params', 'client'],

  data() {
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
    };
  },

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

    // 絞り込み実行
    search() {
      this.form
          .transform((data) => {
            // 値が空の要素を削除
            Object.entries(data).forEach((kv) => {
              if (kv[1] === "" || kv[1] === null) {
                delete data[kv[0]];
              }
            });

            return {...data};
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
    }
  }
}
</script>
