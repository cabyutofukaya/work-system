<template>
  <Layout>
    <v-container>
      <h3 class="mb-4">会議記録一覧</h3>

      <v-card flat tile>
        <v-card-text>
          <v-row>
            <v-col>
              <v-btn :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
                :color="formParamsCount ? 'warning' : undefined">
                <v-icon left>
                  mdi-filter-outline
                </v-icon>
                絞り込み<span v-if="formParamsCount">({{ formParamsCount }})</span>
              </v-btn>

            </v-col>

            <v-col class="text-right">
              <a :href="$route('meetings.create')">
                <v-btn :class="{ 'mb-2': $vuetify.breakpoint.xs }" :small="$vuetify.breakpoint.xs" color="primary">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  会議記録を作成する
                </v-btn>
              </a>

              <v-btn :to="$route('meetings.mine')" :small="$vuetify.breakpoint.xs" class="ml-2">
                <v-icon edit>
                  mdi-playlist-edit
                </v-icon>
                自分の会議記録を管理
              </v-btn>

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
                    開催日時
                  </v-col>
                  <v-col cols="12" sm="6">
                    会議名
                  </v-col>
                  <v-col cols="12" sm="2">
                    名前
                  </v-col>
                  <v-col cols="12" sm="1" class="text-center">
                    コメント
                  </v-col>
                </v-row>
              </v-list-item-content>
            </v-list-item>

            <v-divider class="mx-4"></v-divider>
          </template>

          <div v-for="meeting in meetings['data']" :key="meeting.id">

            <Link as="v-list-item" :href="$route('meetings.show', { id: meeting.id })" target="_blank"
              dusk="meetingShow">
            <v-list-item-content>
              <v-row :class="{ 'grey': meeting['is_visited'], 'lighten-4': meeting['is_visited'] }">
                <template>
                  <v-col sm="2" :class="{ 'font-weight-bold': !meeting['is_visited'] }">
                    {{ meeting.started_at }}
                  </v-col>
                  <v-col sm="6" :class="{ 'font-weight-bold': !meeting['is_visited'] }">
                    {{ meeting.title }}
                  </v-col>
                  <v-col sm="2" :class="{ 'font-weight-bold': !meeting['is_visited'] }">
                    {{ meeting.user.name }}
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
            </Link>


            <v-divider class="mx-4"></v-divider>
          </div>

          <div v-if="!meetings['data'].length">
            <v-list-item>
              <v-list-item-content>
                <v-list-item-subtitle>
                  条件に一致する会議記録は見つかりませんでした
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-divider class="mx-4"></v-divider>
          </div>
        </v-list>

        <v-card-text>
          <v-pagination v-model="page" :length="length" @input="changePage"></v-pagination>
        </v-card-text>
      </v-card>


    </v-container>
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
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ['meetings', 'form_params'],

  data() {
    return {
      // ページ情報の初期値
      page: Number(this.meetings['current_page']),
      length: Number(this.meetings['last_page']),

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
      let url = new URL(this.meetings["last_page_url"]);

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

          return { ...data };
        })
        .get(this.$route('meetings.index'), {
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
