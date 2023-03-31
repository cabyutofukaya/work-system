<template>
  <Layout>
    <v-dialog
        v-model="dialog"
        :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
        persistent
        no-click-animation
    >
      <v-form>
        <v-card
            flat tile
        >
          <v-card-title>
            ログイン
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-text-field
                  dense filled
                  label="ログインID"
                  name="username"
                  v-model="form.username"
                  maxlength="200"
                  :error="Boolean(form.errors.username)"
                  :error-messages="form.errors.username"
                  @keydown.enter="login"
              ></v-text-field>

              <v-text-field
                  dense filled
                  type="password"
                  label="パスワード"
                  name="password"
                  v-model="form.password"
                  maxlength="200"
                  :error="Boolean(form.errors.password)"
                  :error-messages="form.errors.password"
                  @keydown.enter="login"
              ></v-text-field>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Button
                color="primary"
                :small="$vuetify.breakpoint.xs"
                @click.native="login"
                :loading="loading['login']"
            >
              ログイン
            </Button>
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
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  data() {
    return {
      dialog: true,
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
