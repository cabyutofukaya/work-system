<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー情報の編集
      </v-card-title>

      <v-card-text>
        <form @submit.prevent="updatePassword">
          <div class="description-form">
            <v-row>
              <v-col cols="12" sm="4">現在のパスワード</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" type="password" autocomplete="new-password"
                  name="current_password" v-model="formPassword.current_password" maxlength="200"
                  :error="Boolean(formPassword.errors.current_password)"
                  :error-messages="formPassword.errors.current_password"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">新しいパスワード</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" type="password" name="password"
                  v-model="formPassword.password" maxlength="200" :error="Boolean(formPassword.errors.password)"
                  :error-messages="formPassword.errors.password"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">新しいパスワード(確認)</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" type="password" hint="確認のためもう一度入力してください"
                  persistent-hint name="password_confirmation" v-model="formPassword.password_confirmation"
                  maxlength="200" :error="Boolean(formPassword.errors.password_confirmation)"
                  :error-messages="formPassword.errors.password_confirmation"></v-text-field>
              </v-col>
            </v-row>
          </div>

          <v-row>
            <v-col class="text-right">
              <Button color="primary" :small="$vuetify.breakpoint.xs" type="submit"
                :loading="loading['user-password-update']">
                <v-icon left>
                  mdi-content-save-edit-outline
                </v-icon>
                この内容でメンバーパスワードを更新する
              </Button>
            </v-col>
          </v-row>
        </form>
      </v-card-text>

      <v-divider class="my-4"></v-divider>

      <v-card-text>
        <form @submit.prevent="updateProfile">
          <div class="description-form">

            <v-row>
              <v-col cols="12" sm="4">メンバー名(カナ)</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="name_kana" v-model="formProfile.name_kana"
                  maxlength="200" :error="Boolean(formProfile.errors.name_kana)"
                  :error-messages="formProfile.errors.name_kana"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">メールアドレス</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="email" v-model="formProfile.email"
                  maxlength="200" :error="Boolean(formProfile.errors.email)"
                  :error-messages="formProfile.errors.email"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">電話番号</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" hint="半角数字とハイフンのみで入力してください" persistent-hint
                  name="tel" v-model="formProfile.tel" maxlength="200" :error="Boolean(formProfile.errors.tel)"
                  :error-messages="formProfile.errors.tel"></v-text-field>
              </v-col>
            </v-row>

            <!-- <v-row>
              <v-col cols="12" sm="4">所属部署</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="department"
                  v-model="formProfile.department" maxlength="200" :error="Boolean(formProfile.errors.department)"
                  :error-messages="formProfile.errors.department" disabled></v-text-field>
              </v-col>
            </v-row> -->


            <v-row>
              <v-col cols="12" sm="4">アイコン</v-col>
              <v-col>
                <v-file-input dense filled prepend-icon="" prepend-inner-icon="mdi-paperclip" id="img_file"  v-model="formProfile.img_file"
                  :error="Boolean(formProfile.errors.img_file)"
                  :error-messages="formProfile.errors.img_file"></v-file-input>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">趣味</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="hobby"
                  v-model="formProfile.hobby" maxlength="200" :error="Boolean(formProfile.errors.hobby)"
                  :error-messages="formProfile.errors.hobby"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">特技</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="skill"
                  v-model="formProfile.skill" maxlength="200" :error="Boolean(formProfile.errors.skill)"
                  :error-messages="formProfile.errors.skill"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">好きな食べ物</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="food"
                  v-model="formProfile.food" maxlength="200" :error="Boolean(formProfile.errors.food)"
                  :error-messages="formProfile.errors.food"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">好きな旅行先</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="trip"
                  v-model="formProfile.trip" maxlength="200" :error="Boolean(formProfile.errors.trip)"
                  :error-messages="formProfile.errors.trip"></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">自己紹介を一言でどうぞ</v-col>
              <v-col>
                <v-textarea
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="free"
                    v-model="formProfile.free"
                    :error="Boolean(formProfile.errors.free)"
                    :error-messages="formProfile.errors.free"
                ></v-textarea>
              </v-col>
            </v-row>

          

          </div>

          <v-row>
            <v-col class="text-right">
              <Button color="primary" :small="$vuetify.breakpoint.xs" type="submit"
                :loading="loading['user-profile-information-update']">
                <v-icon left>
                  mdi-content-save-edit-outline
                </v-icon>
                この内容でメンバー情報を更新する
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
import { Link } from "@inertiajs/inertia-vue";
import _ from "lodash";

export default {
  components: { Layout, Link },

  props: ['user'],

  data() {
    return {
      // Inertia Formヘルパ
      formPassword: this.$inertia.form('UserProfileEditPassword', {
        current_password: "",
        password: "",
        password_confirmation: "",
      }),
      formProfile: this.$inertia.form('UserProfileEdit', {
        _method: 'put',
        ...this.user,
      }),
      loading: {}
    };
  },

  methods: {
    // メンバーパスワード更新実行
    updatePassword: function () {
      this.formPassword.put(this.$route('user-password.update'), {
        preserveScroll: true,
        errorBag: 'updatePassword',
        onStart: () => this.$set(this.loading, "user-password-update", true),
        onSuccess: () => {
          this.$toasted.show('パスワードの変更を完了しました');
          this.formPassword.reset();
        },
        onFinish: () => this.$set(this.loading, "user-password-update", false),
      })
    },

    // メンバー情報更新実行
    updateProfile: function () {
      this.formProfile.post(this.$route('user-profile-information.update'), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        preserveScroll: true,
        errorBag: 'updateProfileInformation',
        onStart: () => this.$set(this.loading, "user-profile-information-update", true),
        onSuccess: () => this.$toasted.show('メンバー情報の変更を保存しました'),
        onFinish: () => this.$set(this.loading, "user-profile-information-update", false),
      });
    },
   
  }
}
</script>
