<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text>
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

      <v-list>
        <div v-for="user in users" :key="user.id">
          <v-divider class="mx-4"></v-divider>

          <Link as="v-list-item" :href="$route('users.show', {id: user.id})" dusk="userShow">
            <v-list-item-content>
              <v-list-item-title>
                {{ user["name"] }}
              </v-list-item-title>

              <v-list-item-subtitle>
                ID: {{ user["username"] }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </Link>
        </div>
      </v-list>

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

  props: ["users"],
}
</script>
