<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-calendar</v-icon>
          社内ToDoリストの編集
        </v-card-title>

        <form @submit.prevent="update">
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
                    :loading="loading['update']"
                >
                  <v-icon left>
                    mdi-content-save-edit-outline
                  </v-icon>
                  この内容で更新する
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

  props: ['users', 'office_todo'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form(`OfficeTodosEdit${this.office_todo.id}`, {
        ...this.office_todo,
        participants: this.office_todo["office_todo_participants"].map((participant) => participant["user_id"]),
      }),

      loading: {}
    }
  },

  mounted() {
    // input datetime-localで解釈できる YYYY-MM-DDTHH:SS フォーマットに変換
    this.form.scheduled_at = this.form.scheduled_at.replace(" ", "T");
  },

  methods: {
    // ToDo情報更新
    update: function () {
      this.form.put(this.$route('office-todos.update', {office_todo: this.office_todo.id}), {
        onStart: () => this.$set(this.loading, "update", true),
        onSuccess: () => this.$toasted.show('ToDo情報を更新しました'),
        onFinish: () => this.$set(this.loading, "update", false),
      })
    }
  }
}
</script>
