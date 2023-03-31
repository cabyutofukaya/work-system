<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text v-if="Number(user.id) === Number($page.props.auth.user.id)">
        <v-row>
          <v-col class="text-right">
            <Button
                :small="$vuetify.breakpoint.xs"
                :to="$route('user-profile-information.edit')"
            >
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-text>
        <div class="description-list">
          <v-row>
            <v-col cols="12" sm="4">メンバーID</v-col>
            <v-col>
              {{ user.username }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">メンバー名</v-col>
            <v-col>
              {{ user.name }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">登録日時</v-col>
            <v-col>
              {{ user.created_at }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">メールアドレス</v-col>
            <v-col>
              <a :href="'mailto:' + user['email']">{{ user["email"] }}</a>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">電話番号</v-col>
            <v-col>
              <a :href="'tel:' + user['tel']">{{ user["tel"] }}</a>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">所属部署</v-col>
            <v-col>
              {{ user.department }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">担当商材</v-col>
            <v-col>
              <div v-for="product in user['products']">
                {{ product.name }}
              </div>
            </v-col>
          </v-row>
        </div>
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

export default {
  components: {Layout, Link},

  props: ['user'],
}
</script>
