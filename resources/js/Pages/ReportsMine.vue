<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document</v-icon>
        自分の日報
      </v-card-title>

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
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4"></v-divider>

      <v-list>
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="2">
                  日付
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
                <v-col cols="12" sm="1">
                </v-col>
                <v-col cols="12" sm="1">
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="report in reports['data']" :key="report.id">
            <v-list-item>
            <v-list-item-content>
              <v-row :class="{'grey': report['is_private'], 'lighten-4': report['is_private']}">
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2">
                    <!-- {{ report.date }} -->

                    {{ report.created_at.substr(0,16) }}

                  </v-col>
                  <v-col sm="6">
                    <a :href="$route('reports.show', {id: report.id})">
                    <span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                    <span v-if="report.draft_flg == 1">(下書き)</span>
                  </a>
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1">
                      {{ report.created_at.substr(0,16) }}
                      <!-- {{ report.date }} -->
                    </div>
                    <div>
                      <a :href="$route('reports.show', {id: report.id})"></a><span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span> 
                      <span v-if="report.draft_flg == 1">(下書き)</span></a></div>
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

                  <v-col sm="2" class="py-1 text-right">
                    <v-icon
                        v-if="report.is_private"
                        color="warning"
                        class="mr-4"
                    >
                      mdi-eye-off
                    </v-icon>

                    <Button
                        :class="{'mb-2' : $vuetify.breakpoint.xs}"
                        :small="$vuetify.breakpoint.xs"
                        @click.native.prevent="editReport(report.id)"
                        dusk="reportEdit"
                    >
                      <v-icon left>
                        mdi-pencil
                      </v-icon>
                      編集
                    </Button>

                    <Button
                        color="error"
                        class="ml-2"
                        :small="$vuetify.breakpoint.xs"
                        @click.native.prevent="deleteReport(report.id)"
                        :loading="loading['delete-' + report.id]"
                        dusk="reportDelete"
                    >
                      <v-icon left>
                        mdi-delete-outline
                      </v-icon>
                      削除
                    </Button>
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

                    <v-col cols="12" sm="2" class="text-right">
                      <v-icon
                          v-if="report.is_private"
                          color="warning"
                          class="mr-4"
                      >
                        mdi-eye-off
                      </v-icon>

                      <Button
                          :small="$vuetify.breakpoint.xs"
                          @click.native.prevent="editReport(report.id)"
                      >
                        <v-icon left>
                          mdi-pencil
                        </v-icon>
                        編集
                      </Button>

                      <Button
                          color="error"
                          :small="$vuetify.breakpoint.xs"
                          class="ml-2"
                          @click.native.prevent="deleteReport(report.id)"
                          :loading="loading['delete-' + report.id]"
                      >
                        <v-icon left>
                          mdi-delete-outline
                        </v-icon>
                        削除
                      </Button>
                    </v-col>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </v-list-item>
     

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
              <v-list-item>
                <v-text-field
                    dense filled clearable
                    prepend-inner-icon="mdi-pencil"
                    label="ワード検索"
                    hint="仕事内容・本文・会社名(ふりがな)・面談者・面談内容から指定したワードを含む項目に絞り込みます。YYYYMMDDを指定すると日付で検索できます" persistent-hint
                    name="word"
                    v-model="form.word"
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

              <v-list-item class="mt-4">
                <v-switch dense filled class="mt-0 ml-2" color="warning" label="コメント未読あり" hint="コメント未読あり"
                  persistent-hint name="is_readed" v-model="form.is_readed" true-value="1" false-value=""
                  :error="Boolean(form.errors.is_readed)" :error-messages="form.errors.is_readed"></v-switch>
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

<style>
.is_readed{
  color: rgb(240, 49, 42);
}
</style>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['reports'],

  data() {
    // 検索パラメータリスト
    const searchParams = ["word", "only_complaint","is_readed"]

    // 検索フォーム初期値をパラメータから取得
    let formParams = {};
    searchParams.forEach((param) => {
      // 値がない項目にはnullを設定
      formParams[param] = this.$route().params[param] ?? null;
    });

    return {
      // ページ情報の初期値
      page: Number(this.reports['current_page']),
      length: Number(this.reports['last_page']),

      // 検索パラメータリスト
      searchParams: searchParams,

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(formParams),
      formParamsCount: Object
          .entries(formParams)
          .filter(function (param) {
            return param[1] && param[1].length !== 0;
          }).length,

      loading: {},
    };
  },

  methods: {
    // ページ移動
    changePage() {
      // クエリパラメータのうち検索パラメータのみを取得する
      let searchParamValues = Object.assign(...this.searchParams.map(key => ({[key]: this.$route().params[key]})));

      // パラメータにページを追加
      if (1 < this.page) {
        searchParamValues["page"] = this.page;
      }

      // パラメータを引き継いでページ移動
      this.$inertia.get(this.$route('reports.mine'), {
        ...searchParamValues
      });
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
          .get(this.$route('reports.mine'), {
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

    // 日報編集
    editReport: function (id) {
      this.$inertia.get(this.$route('reports.edit', {report: id}));
    },

    // 日報削除
    deleteReport: function (id) {
      this.$confirm('この日報を削除してよろしいですか？<br>削除した項目を元に戻すことはできません<br>営業日報に含まれる会社の評価も削除されます').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('reports.destroy', {report: id}), {
            onStart: () => this.$set(this.loading, "delete-" + id, true),
            onSuccess: () => this.$toasted.show('日報を削除しました'),
            onFinish: () => this.$set(this.loading, "delete-" + id, false),
          })
        }
      })
    },
  }
}
</script>
