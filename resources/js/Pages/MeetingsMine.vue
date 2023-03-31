<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document-edit</v-icon>
        自分の議事録
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
                  :to="$route('meetings.create')"
            >
              <v-icon left>
                mdi-plus
              </v-icon>
              議事録を作成する
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
                  開催日時
                </v-col>
                <v-col cols="12" sm="6">
                  会議名
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

        <div v-for="meeting in meetings['data']" :key="meeting.id">
          <Link as="v-list-item" :href="$route('meetings.show', {id: meeting.id})" dusk="meetingShow">
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="2">
                  {{ meeting.started_at }}
                </v-col>
                <v-col cols="12" sm="6">
                  {{ meeting.title }}
                </v-col>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col cols="12" sm="1" class="text-center">
                    {{ meeting["meeting_likes_count"] }}
                  </v-col>

                  <v-col cols="12" sm="1" class="text-center">
                    {{ meeting["meeting_comments_count"] }}
                  </v-col>

                  <v-col cols="12" sm="2" class="py-1 text-right">
                    <Button
                        :class="{'mb-2' : $vuetify.breakpoint.xs}"
                        :small="$vuetify.breakpoint.xs"
                        @click.native.prevent="editMeeting(meeting.id)"
                        dusk="meetingEdit"
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
                        @click.native.prevent="deleteMeeting(meeting.id)"
                        :loading="loading['delete-' + meeting.id]"
                        dusk="meetingDelete"
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
                  <v-col cols="12" sm="3" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ meeting["meeting_likes_count"] }}
                    </v-chip>

                    <v-chip small class="mr-2">
                      コメント
                      {{ meeting["meeting_comments_count"] }}
                    </v-chip>

                    <v-col cols="12" sm="2" class="text-right">
                      <Button
                          :small="$vuetify.breakpoint.xs"
                          @click.native.prevent="editMeeting(meeting.id)"
                          dusk="meetingEdit"
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
                          @click.native.prevent="deleteMeeting(meeting.id)"
                          :loading="loading['delete-' + meeting.id]"
                          dusk="meetingDelete"
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
                    maxlength="200"
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

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['meetings'],

  data() {
    // 検索パラメータリスト
    const searchParams = ["word"]

    // 検索フォーム初期値をパラメータから取得
    let formParams = {};
    searchParams.forEach((param) => {
      // 値がない項目にはnullを設定
      formParams[param] = this.$route().params[param] ?? null;
    });

    return {
      // ページ情報の初期値
      page: Number(this.meetings['current_page']),
      length: Number(this.meetings['last_page']),

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
      this.$inertia.get(this.$route('meetings.mine'), {
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
          .get(this.$route('meetings.mine'), {
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

    // 議事録編集
    editMeeting: function (id) {
      this.$inertia.get(this.$route('meetings.edit', {meeting: id}));
    },

    // 議事録削除
    deleteMeeting: function (id) {
      this.$confirm('この議事録を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('meetings.destroy', {meeting: id}), {
            onStart: () => this.$set(this.loading, "delete-" + id, true),
            onSuccess: () => this.$toasted.show('議事録を削除しました'),
            onFinish: () => this.$set(this.loading, "delete-" + id, false),
          })
        }
      })
    },
  }
}
</script>
