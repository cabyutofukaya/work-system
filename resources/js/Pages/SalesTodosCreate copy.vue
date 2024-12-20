<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-calendar</v-icon>
          営業ToDoリストの作成
        </v-card-title>

        <form @submit.prevent="create">
          <v-card-text>
            <div class="description-form">
              <v-row>
                <v-col cols="12" sm="4">日時</v-col>
                <v-col>
                  <v-text-field
                      prepend-icon="mdi-calendar"
                      type="datetime-local"
                      name="scheduled_at"
                      v-model="form.scheduled_at"
                      :error="Boolean(form.errors.scheduled_at)"
                      :error-messages="form.errors.scheduled_at"
                  ></v-text-field>
                </v-col>
              </v-row>

              <!-- 会社 -->
              <v-row>
                <v-col cols="12" sm="4">会社</v-col>
                <v-col>
                  <!-- 会社リストの絞り込み -->
                  <div class="mx-8">
                    <v-row>
                      <v-col cols="12" sm="6">
                        <v-text-field
                            dense outlined clearable
                            label="会社名(ふりがな)から検索"
                            hint="文字を含む会社だけをリストに表示します" persistent-hint
                            name="clientsFilterForm.name"
                            v-model="clientsFilterForm.name"
                            maxlength="200"
                            @input="getClients()"
                        ></v-text-field>
                      </v-col>

                      <v-col cols="12" sm="6">
                        <v-select
                            dense outlined clearable
                            label="会社種別"
                            hint="該当する会社だけをリストに表示します" persistent-hint
                            :items="client_types"
                            item-value="id"
                            item-text="name"
                            name="clientsFilterForm.client_type_id"
                            v-model="clientsFilterForm.client_type_id"
                            @change="clientsFilterForm.client_type_taxibus_category = ''; clientsFilterForm.genre_id = ''; getClients()"
                        >
                        </v-select>
                      </v-col>

                    </v-row>
                    <v-row>
                      <v-col cols="12" sm="6">
                        <v-select
                            dense outlined clearable
                            label="カテゴリー(タクシー・バス会社のみ)"
                            hint="該当する会社だけをリストに表示します" persistent-hint
                            :items="client_type_taxibus_categories"
                            item-value="id"
                            item-text="name"
                            name="clientsFilterForm.client_type_taxibus_category"
                            v-model="clientsFilterForm.client_type_taxibus_category"
                            :disabled="clientsFilterForm.client_type_id !== 'taxibus'"
                            @change="getClients()"
                        >
                        </v-select>
                      </v-col>

                      <v-col cols="12" sm="6">
                        <v-select
                            dense outlined clearable
                            label="ジャンル"
                            hint="該当する会社だけをリストに表示します" persistent-hint
                            :items="genresFiltered"
                            item-value="id"
                            item-text="name"
                            name="clientsFilterForm.genre_id"
                            v-model="clientsFilterForm.genre_id"
                            :disabled="!Boolean(clientsFilterForm.client_type_id)"
                            @change="getClients()"
                        >
                        </v-select>
                      </v-col>
                    </v-row>
                  </div>

                  <div class="mt-4 font-weight-bold">
                    <template v-if="clients_count">
                      {{ clients_total_count }}件中<span class="red--text">{{ clients_count }}</span>件中該当
                      <template v-if="this.$page.props.errors.event">
                        <br>{{ this.$page.props.errors.event }}
                      </template>
                    </template>
                    <template v-else>
                      会社を検索する条件を指定してください
                    </template>
                  </div>

                  <!-- 会社リスト -->
                  <v-select
                      v-if="!clientsListEnable"
                      dense filled clearable
                      disabled
                  >
                  </v-select>
                  <v-select
                      v-if="clientsListEnable"
                      dense filled clearable
                      :items="clients"
                      item-value="id"
                      name="client_id"
                      v-model="form.client_id"
                      :error="Boolean(form.errors.client_id)"
                      :error-messages="form.errors.client_id"
                  >
                    <!-- カスタム選択済み表示 -->
                    <template v-slot:selection="{ item }">
                      <v-img
                          contain
                          height="2em"
                          width="2em"
                          max-height="2em"
                          max-width="2em"
                          class="my-2 mr-2"
                          :src="item['icon_image_url']" alt=""
                      ></v-img>
                      {{ item.name }}
                    </template>
                    <!-- カスタム選択肢表示 -->
                    <template v-slot:item="{ item }">
                      <v-img
                          contain
                          height="2em"
                          width="2em"
                          max-height="2em"
                          max-width="2em"
                          class="my-2 mr-2"
                          :src="item['icon_image_url']" alt=""
                      ></v-img>
                      {{ item.name }}
                    </template>
                  </v-select>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">相手先担当者</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="contact_person"
                      v-model="form.contact_person"
                      maxlength="200"
                      :error="Boolean(form.errors.contact_person)"
                      :error-messages="form.errors.contact_person"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">要件</v-col>
                <v-col>
                  <v-textarea
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="description"
                      v-model="form.description"
                      maxlength="200" counter="200"
                      :error="Boolean(form.errors.description)"
                      :error-messages="form.errors.description"
                  ></v-textarea>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">社内担当者22</v-col>
                <v-col>

                  <v-autocomplete  dense filled 
                      :items="users"
                      item-value="id"
                      item-text="name"
                      hint="複数の担当者を設定できます" persistent-hint
                      name="participants"
                      v-model="form.participants"
                      :error="Boolean(form.errors.participants)"
                      :error-messages="form.errors.participants"></v-autocomplete>
<!--                       
                  <v-autocomplete
                      dense filled multiple
                      :items="users"
                      item-value="id"
                      item-text="name"
                      hint="複数の担当者を設定できます" persistent-hint
                      name="participants"
                      v-model="form.participants"
                      :error="Boolean(form.errors.participants)"
                      :error-messages="form.errors.participants"
                  ></v-autocomplete> -->
                </v-col>

                <v-autocomplete  dense filled 
                      :items="users"
                      item-value="id"
                      item-text="name"
                      hint="複数の担当者を設定できます" persistent-hint
                      name="participants"
                      v-model="form.participants"
                      :error="Boolean(form.errors.participants)"
                      :error-messages="form.errors.participants"></v-autocomplete>
              </v-row>
            </div>

            <v-row>
              <v-col class="text-right">
                <Button
                    :small="$vuetify.breakpoint.xs"
                    color="primary"
                    type="submit"
                    :loading="loading['create']"
                >
                  <v-icon left>
                    mdi-content-save-outline
                  </v-icon>
                  この内容で作成する
                </Button>
              </v-col>
            </v-row>
          </v-card-text>
        </form>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <BackButton></BackButton>
        </v-card-text>
      </v-card>
    </div>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['client_types', 'client_type_taxibus_categories', 'genres', 'clients_total_count', 'clients_count', 'clients', 'users'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form('SalesTodosCreate', {
        // 会社ページ「直近の営業Todo」から遷移した場合に設定される会社ID
        client_id: Number(this.$route().params["client_id"]) ?? null,
        contact_person: null,
        scheduled_at: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10) + "T12:00",
        description: null,
        participants: []
      }),

      // 会社絞り込みフォーム
      clientsFilterForm: {
        name: "",
        client_type_id: "",
        client_type_taxibus_category: "",
        genre_id: "",
      },

      // 会社絞り込みフォーム 会社リスト表示
      clientsListEnable: false,

      loading: {}
    };
  },

  computed: {
    // ジャンルリストの絞り込み
    genresFiltered: function () {
      return this.genres.filter((genre) => {
        return genre.client_type_id === this.clientsFilterForm.client_type_id;
      })
    },
  },

  mounted() {
    // 会社ページ「この会社のToDoを追加する」からの遷移であれば該当の会社のみがリストを取得
    if (this.form.client_id) {
      this.getClients(this.form.client_id);
    }
  },

  methods: {
    // 会社リストの更新
    getClients: function (client_id = null) {
      let param;
      if (client_id) {
        // 会社IDが指定された場合は該当の会社のみがリストを取得
        param = {client_id: client_id};
      } else {
        // フォーム内容に設定された条件の会社リストを取得
        param = this.clientsFilterForm;
      }

      this.$inertia.post(this.$route('sales-todos.create'), param, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['clients_count', 'clients', 'errors'],
        onSuccess: () => {
          this.clientsListEnable = true;
        },
        onError: errors => {
          this.clientsListEnable = false;

          // バリデーションエラーをトースト表示する
          // フロント側チェックを行っているため発生しない前提
          //this.$toasted.error(Object.values(errors).join("\n"), {type: 'error'});
        },
      })
    },

    // ToDo情報作成
    create: function () {
      this.form.post(this.$route('sales-todos.store'), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('ToDoを作成しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
