<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document-edit</v-icon>
        議事録
      </v-card-title>

      <v-card-text class="py-0">
        <ul>
          <li>最近開催されたものから順に表示されます。</li>
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
                  :to="$route('meetings.create')"
            >
              <v-icon left>
                mdi-plus
              </v-icon>
              議事録を作成する
            </Link>

            <Link as="Button"
                  :to="$route('meetings.mine')"
                  :small="$vuetify.breakpoint.xs"
                  class="ml-2"
            >
              <v-icon edit>
                mdi-playlist-edit
              </v-icon>
              自分の議事録を管理
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
                  開催日時
                </v-col>
                <v-col cols="12" sm="6">
                  会議名
                </v-col>
                <v-col cols="12" sm="2">
                  名前
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

        <div v-for="meeting in meetings['data']" :key="meeting.id">
          <Link as="v-list-item" :href="$route('meetings.show', {id: meeting.id})" dusk="meetingShow">
            <v-list-item-content>
              <v-row :class="{'grey': meeting['is_visited'], 'lighten-4': meeting['is_visited']}">
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2" :class="{'font-weight-bold': !meeting['is_visited']}">
                    {{ meeting.started_at }}
                  </v-col>
                  <v-col sm="6" :class="{'font-weight-bold': !meeting['is_visited']}">
                    {{ meeting.title }}
                  </v-col>
                  <v-col sm="2" :class="{'font-weight-bold': !meeting['is_visited']}">
                    {{ meeting.user.name }}
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1" :class="{'font-weight-bold': !meeting['is_visited']}">{{ meeting.started_at }}</div>
                    <div class="mb-1" :class="{'font-weight-bold': !meeting['is_visited']}">{{ meeting.title }}</div>
                    <div :class="{'font-weight-bold': !meeting['is_visited']}">{{ meeting.user.name }}</div>
                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ meeting["meeting_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center">
                    {{ meeting["meeting_comments_count"] }}
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ meeting["meeting_likes_count"] }}
                    </v-chip>
                    <v-chip small class="mr-2">
                      コメント
                      {{ meeting["meeting_comments_count"] }}
                    </v-chip>
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
                条件に一致する議事録は見つかりませんでした
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
                    hint="会議名・名前から指定したワードを含む項目に絞り込みます。YYYYMMDDを指定すると日付で検索できます" persistent-hint
                    name="word"
                    v-model="form.word"
                    :error="Boolean(form.errors.word)"
                    :error-messages="form.errors.word"
                    :autofocus="!$vuetify.breakpoint.xs"
                ></v-text-field>
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

            return {...data};
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
