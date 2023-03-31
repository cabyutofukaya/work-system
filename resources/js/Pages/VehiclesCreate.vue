<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-car</v-icon>
          保有車両の編集
        </v-card-title>

        <form @submit.prevent="create">
          <v-card-text>
            <div class="description-form">
              <v-row>
                <v-col cols="12" sm="4">車両画像</v-col>
                <v-col>
                  <v-file-input
                      dense filled
                      prepend-icon=""
                      prepend-inner-icon="mdi-paperclip"
                      v-model="form._image"
                      :error="Boolean(form.errors._image)"
                      :error-messages="form.errors._image"
                  ></v-file-input>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">車両名</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="name"
                      v-model="form.name"
                      maxlength="200"
                      :error="Boolean(form.errors.name)"
                      :error-messages="form.errors.name"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">車両説明</v-col>
                <v-col>
                  <v-textarea
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      v-model="form.description"
                      :error="Boolean(form.errors.description)"
                      :error-messages="form.errors.description"
                  ></v-textarea>
                </v-col>
              </v-row>
            </div>

            <v-col class="text-right">
              <Button
                  color="primary"
                  :small="$vuetify.breakpoint.xs"
                  type="submit"
                  :loading="loading['create']"
              >
                <v-icon left>
                  mdi-content-save-outline
                </v-icon>
                この内容で作成する
              </Button>
            </v-col>
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

  props: ['client'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form({
        _image: null,
        type: this.$route().params.type,
        name: null,
        description: null,
      }),
      loading: {}
    }
  },

  methods: {
    // 保湯車両情報作成
    create: function () {
      this.form.post(this.$route('clients.vehicles.store', {client: this.client.id}), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => {
          this.$toasted.show('保有車両情報を作成しました');

          // id="vehicles-TYPE"へのスクロール
          this.$vuetify.goTo("#vehicles-" + this.form.type);
        },
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }

}
</script>
