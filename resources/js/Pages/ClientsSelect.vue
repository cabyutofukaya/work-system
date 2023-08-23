<template>
  <Layout>

    <v-card flat tile>
      <v-card-title>
        <v-icon dark left></v-icon>
        新規登録会社タイプ選択
      </v-card-title>

      <a href="/client-types/taxibus/clients/create">
        <v-btn tile depressed class="text-capitalize mt-4" color="#969797" dark>
          <v-icon left>
            mdi-car
          </v-icon>
          タクシー・バス会社
        </v-btn>
      </a>

      <a href="/client-types/truck/clients/create">
        <v-btn tile depressed class="text-capitalize mt-4" color="#969797" dark>
          <v-icon left>
            mdi-truck
          </v-icon>
          トラック会社
        </v-btn>
      </a>

      <a href="/client-types/travel/clients/create">
        <v-btn tile depressed class="text-capitalize mt-4" color="#969797" dark>
          <v-icon left>
            mdi-office-building
          </v-icon>
          旅行業者など
        </v-btn>
      </a>
    </v-card>



  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  data() {
    return {
      // ページ情報の初期値
      loading: {},
    };
  },

  methods: {

  }
}
</script>
