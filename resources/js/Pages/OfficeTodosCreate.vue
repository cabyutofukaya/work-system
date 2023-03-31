<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-calendar</v-icon>
          社内ToDoリストの作成
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

              <v-row>
                <v-col cols="12" sm="4">タイトル</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="title"
                      v-model="form.title"
                      maxlength="200"
                      :error="Boolean(form.errors.title)"
                      :error-messages="form.errors.title"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">メモ</v-col>
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
                <v-col cols="12" sm="4">社内担当者</v-col>
                <v-col>
                  <v-select
                      dense filled multiple
                      :items="users"
                      item-value="id"
                      item-text="name"
                      hint="複数の担当者を設定できます" persistent-hint
                      name="participants"
                      v-model="form.participants"
                      :error="Boolean(form.errors.participants)"
                      :error-messages="form.errors.participants"
                  ></v-select>
                </v-col>
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

  props: ['users'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form('OfficeTodosCreate', {
        scheduled_at: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10) + "T12:00",
        title: null,
        description: null,
        participants: []
      }),

      loading: {}
    }
  },

  methods: {
    // ToDo情報作成
    create: function () {
      this.form.post(this.$route('office-todos.store'), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('ToDoを作成しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
