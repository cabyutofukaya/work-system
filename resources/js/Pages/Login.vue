<template>
  <Layout>
    <v-dialog v-model="dialog" :max-width="$vuetify.breakpoint.smAndUp ? '400px' : 'unset'" persistent
      no-click-animation>
      <v-form>
        <v-card>

          <v-card-text>
            <v-list>
              <v-text-field prepend-icon="mdi-account-circle" label="ユーザ名" v-model="form.username"
                :error="Boolean(form.errors.username)" :error-messages="form.errors.username" />
              <v-text-field v-bind:type="showPassword ? 'text' : 'password'"
                @click:append="showPassword = !showPassword" prepend-icon="mdi-lock"
                v-bind:append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" label="パスワード" v-model="form.password"
                :error="Boolean(form.errors.password)" :error-messages="form.errors.password" />
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <v-btn color="primary" :small="$vuetify.breakpoint.xs" @click.native="login" :loading="loading['login']">
              ログイン
            </v-btn>
          </v-card-text>
        </v-card>
      </v-form>
    </v-dialog>
  </Layout>
</template>

<style scoped>
.container {
  max-width: 400px;
}
</style>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  data() {
    return {
      dialog: true,
      showPassword: false,
      // フォームヘルパ
      form: this.$inertia.form({
        username: null,
        password: null,
      }),
      loading: {}
    };
  },

  methods: {
    // ログイン
    login() {
      this.form.post(this.$route('login'), {
        replace: true,
        onStart: () => this.$set(this.loading, "login", true),
        onSuccess: () => {
          this.$toasted.show('ログインしました');

          // Sentryにユーザ情報を設定
          this.$sentry.configureScope((scope) => {
            scope.setUser(this.$page.props.auth.user);
          });
        },
        onFinish: () => this.$set(this.loading, "login", false),
      })
    }
  }
};
</script>
