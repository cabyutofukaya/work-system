<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document-edit</v-icon>
        議事録の作成
      </v-card-title>

      <v-card-text>
        <form @submit.prevent="create">
          <div class="description-form">
            <v-row>
              <v-col cols="12" sm="4">会議名</v-col>
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
              <v-col cols="12" sm="4">開催日時</v-col>
              <v-col>
                <v-text-field
                    prepend-icon="mdi-calendar"
                    type="datetime-local"
                    name="started_at"
                    v-model="form.started_at"
                    :error="Boolean(form.errors.started_at)"
                    :error-messages="form.errors.started_at"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">参加者</v-col>
              <v-col>
                <v-textarea
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    rows="3"
                    name="participants"
                    v-model="form.participants"
                    :error="Boolean(form.errors.participants)"
                    :error-messages="form.errors.participants"
                ></v-textarea>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">議事内容</v-col>
              <v-col>
                <v-textarea
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="content"
                    v-model="form.content"
                    :error="Boolean(form.errors.content)"
                    :error-messages="form.errors.content"
                ></v-textarea>
              </v-col>
            </v-row>
          </div>

          <v-row>
            <v-col class="text-right">
              <Button
                  color="primary"
                  :small="$vuetify.breakpoint.xs"
                  @click.native="create"
                  :loading="loading['create']"
              >
                <v-icon left>
                  mdi-content-save-outline
                </v-icon>
                この内容で議事録を作成する
              </Button>
            </v-col>
          </v-row>
        </form>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";
import _ from "lodash";

export default {
  components: {Layout, Link},

  props: [],

  data() {
    return {
      // Inertia Formヘルパ
      form: this.$inertia.form('MeetingsCreate', {
        title: "",
        started_at: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10) + "T12:00",
        participants: "",
        content: "",
      }),
      loading: {}
    };
  },

  methods: {
    // 議事録作成実行
    create: function () {
      this.form.post(this.$route('meetings.store'), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('議事録を作成しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
