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

        <form @submit.prevent="update">
          <v-card-text>
            <v-row>
              <v-col class="text-right">
                <Button
                    :small="$vuetify.breakpoint.xs"
                    class="ml-2"
                    color="error"
                    @click.native="destroy"
                    :loading="loading['delete']"
                >
                  この車両情報を削除する
                </Button>
              </v-col>
            </v-row>

            <div class="description-form">
              <v-row>
                <v-col cols="12" sm="4">車両画像</v-col>
                <v-col>
                  <v-file-input
                      dense filled
                      prepend-icon=""
                      prepend-inner-icon="mdi-paperclip"
                      hint="変更する場合のみ指定してください" persistent-hint
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
                  :loading="loading['update']"
              >
                <v-icon left>
                  mdi-content-save-edit-outline
                </v-icon>
                この内容で更新する
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

  props: ['vehicle'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form({
        _method: 'put', // putメソッドではファイルアップロードができないためpostで代替し、Laravelにはputでの受け取りを指示
        _image: null,
        ...this.vehicle,
      }),
      loading: {}
    }
  },

  methods: {
    // 保有車両情報作成
    update: function () {
      this.form.post(this.$route('vehicles.update', {vehicle: this.vehicle.id}), {
        onStart: () => this.$set(this.loading, "update", true),
        onSuccess: () => {
          this.$toasted.show('保有車両情報を更新しました');
        },
        onFinish: () => this.$set(this.loading, "update", false),
      })
    },

    // 保有車両情報削除
    destroy: function () {
      this.$confirm('この営業所を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.form.delete(this.$route('vehicles.destroy', {vehicle: this.vehicle.id}), {
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => {
              this.$toasted.show('保有車両情報を削除しました');

              // id="vehicles-TYPE"へのスクロール
              this.$vuetify.goTo("#vehicles-" + this.vehicle['type']);
            },
            onFinish: () => this.$set(this.loading, "delete", false),
          })
        }
      });
    },
  }

}
</script>
