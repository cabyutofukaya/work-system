<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-calendar</v-icon>
        営業ToDoリスト
      </v-card-title>

      <v-card-text class="py-0">
        <ul>
          <li>日時の近いものから表示されます。</li>
          <li>対応済みの項目は一覧の末尾に移動されます。</li>
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
                  :to="$route('sales-todos.create')"
                  :small="$vuetify.breakpoint.xs"
                  :class="{'mb-2' : $vuetify.breakpoint.xs}"
                  dusk="salesTodoCreate"
            >
              <v-icon left>
                mdi-plus
              </v-icon>
              ToDoを追加する
            </Link>
          </v-col>
        </v-row>
      </v-card-text>

      <v-list class="dense-list py-0">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row class="font-weight-bold">
                <v-col cols="12" sm="3">
                  日時 / 会社 / 相手先担当者
                </v-col>
                <v-col cols="12" sm="5">
                  要件
                </v-col>
                <v-col cols="12" sm="1" style="white-space:nowrap">
                  社内担当者
                </v-col>
                <v-col cols="12" sm="3">

                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="sales_todo in sales_todos['data']" :key="sales_todo.id" :id="'id-' + sales_todo.id">
          <v-list-item>
            <v-list-item-content>
              <v-row :class="{'grey': sales_todo['is_completed'], 'lighten-4': sales_todo['is_completed']}">
                <v-col cols="12" sm="3" class="grey lighten-3 px-6">
                  <div>
                    {{ sales_todo['scheduled_at'] }}
                  </div>

                  <div class="d-flex align-center ">
                    <div>
                      <Link :href="$route('clients.show', { client: sales_todo['client_id']})" dusk="clientShow">
                        <v-list-item-avatar tile>
                          <v-img
                              :src="sales_todo['client']['icon_image_url']" alt=""
                          ></v-img>
                        </v-list-item-avatar>
                      </Link>
                    </div>

                    <div>
                      <div>
                        <Link :href="$route('clients.show', { client: sales_todo['client_id']})" dusk="clientShow">
                          {{ sales_todo['client']['name'] }}
                        </Link>
                      </div>
                      <div class="mt-1">
                        <span v-if="$vuetify.breakpoint.xs">相手先担当者：</span>{{ sales_todo['contact_person'] }}
                      </div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" sm="5">
                  <span style="white-space: pre-line;">{{ sales_todo["description"] }}</span>
                </v-col>

                <!-- 社内担当者 PCビュー -->
                <template v-if="!$vuetify.breakpoint.xs">
                  <v-col cols="1">
                    <div
                        v-for="(sales_todo_participant, index) in sales_todo['sales_todo_participants']" :key="index"
                        class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                        style="white-space:nowrap"
                    >
                      <Link :href="$route('users.show', {user: sales_todo_participant['user_id']})" dusk="userShowParticipant">
                        {{ sales_todo_participant['user']['name'] }}
                      </Link>
                    </div>
                  </v-col>
                </template>

                <!-- 社内担当者 スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" v-if="sales_todo['sales_todo_participants'].length">
                    社内担当者：<span
                      v-for="(sales_todo_participant, index) in sales_todo['sales_todo_participants']" :key="index"
                      class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                  >
                        <Link :href="$route('users.show', {user: sales_todo_participant['user_id']})" dusk="userShowParticipant">
                          {{ sales_todo_participant['user']['name'] }}
                        </Link>
                      </span>
                  </v-col>
                </template>

                <v-col cols="12" sm="3" class="text-right">
                  <Button
                      v-if="!sales_todo['is_completed']"
                      color="primary" dark
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="openSalesTodo(sales_todo.id)"
                      :loading="loading['complete-' + sales_todo.id]"
                      dusk="salesTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      v-if="sales_todo['is_completed']"
                      color="primary" outlined
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="closeSalesTodo(sales_todo.id)"
                      :loading="loading['complete-' + sales_todo.id]"
                      dusk="salesTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      :color="sales_todo['is_completed'] ? 'grey' : undefined"
                      :outlined="sales_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="editSalesTodo(sales_todo.id)"
                      dusk="salesTodoEdit"
                  >
                    <v-icon left>
                      mdi-pencil
                    </v-icon>
                    編集
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      color="error"
                      :outlined="sales_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="deleteSalesTodo(sales_todo.id)"
                      :loading="loading['delete-' + sales_todo.id]"
                      dusk="salesTodoDelete"
                  >
                    <v-icon left>
                      mdi-delete-outline
                    </v-icon>
                    削除
                  </Button>
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4" v-if="!$vuetify.breakpoint.xs"></v-divider>
        </div>
      </v-list>

      <v-card-text v-if="!sales_todos['data'].length">
        まだ営業ToDoはありません
      </v-card-text>

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
                    hint="要件・相手先担当者名・会社名(ふりがな)・社内担当者名から指定したワードを含む項目に絞り込みます。YYYYMMDDを指定すると日付で検索できます" persistent-hint
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
                :loading="loading['search']"
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

  props: ['sales_todos', 'form_params', 'client'],

  data() {
    return {
      // ページ情報の初期値
      page: Number(this.sales_todos['current_page']),
      length: Number(this.sales_todos['last_page']),

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

      loading: {}
    }
  },

  methods: {
    // ページ移動
    changePage() {
      // サーバ側で生成された検索パラメータを含む最終ページURLを取得
      let url = new URL(this.sales_todos["last_page_url"]);

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
          .get(this.$route('sales-todos.index'), {
            onStart: () => this.$set(this.loading, "search", true),
            onSuccess: () => this.closeSearchDialog(),
            onFinish: () => this.$set(this.loading, "search", false),
          });
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;
    },

    // ToDo対応済み
    closeSalesTodo: function (id) {
      this.$confirm(
          "このToDoを対応済みにしてよろしいですか？<br>対応済みに変更すると未対応のリストの後ろに移動されます",
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('sales-todos.complete', {sales_todo: id}), {}, {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを対応済みにしました'),
            onFinish: () => this.$set(this.loading, "complete-" + id, false),
          })
        }
      })
    },

    // ToDo対応済み解除
    openSalesTodo: function (id) {
      this.$confirm(
          'このToDoを対応済みから未対応に戻します。<br>よろしいですか？',
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('sales-todos.complete', {sales_todo: id}), {}, {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを未対応にしました'),
            onFinish: () => this.$set(this.loading, "complete-" + id, false),
          })
        }
      })
    },

    // ToDo編集
    editSalesTodo: function (id) {
      this.$inertia.get(this.$route('sales-todos.edit', {sales_todo: id}));
    },

    // ToDo削除
    deleteSalesTodo: function (id) {
      this.$confirm('このToDoを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('sales-todos.destroy', {sales_todo: id}), {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "delete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを削除しました'),
            onFinish: () => this.$set(this.loading, "delete-" + id, false),
          })
        }
      })
    },
  }
}
</script>
