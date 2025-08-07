<template>
  <Layout>
    <v-card flat tile>
      <v-card-text class="pt-0" v-for="category in categories" :key="category.id">
        <div class="grey lighten-3 px-4 py-2">
          <h3>{{ category.name }}</h3>
        </div>

        <v-list>
          <div v-for="(document, index) in category.documents" :key="document.id">
            <v-divider v-if="index !== 0"></v-divider>

            <v-list-item two-line :href="$route('static', { path: document.path })">
              <v-list-item-icon>
                <v-icon>
                  mdi-file
                </v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-title>
                  {{ document["title"] }}
                </v-list-item-title>

              </v-list-item-content>
            </v-list-item>
          </div>
        </v-list>
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

export default {
  components: { Layout, Link },

  data() {
    return {
      categories: [
        {
          name: "書類",
          documents: [
            { path: "documents/稟議書.doc", title: "稟議書" },
            { path: "documents/休職届.doc", title: "休職届" },
            { path: "documents/登録変更届.pdf", title: "登録変更届（pdf）" },
            { path: "documents/登録変更届.xlsx", title: "登録変更届（xlsx）" },
          ]
        },

        {
          name: "精算用書類",
          documents: [
            { path: "documents/清算書.pdf", title: "経費清算書（pdf）" },
            { path: "documents/清算書.xls", title: "経費精算書（xls）" },
          ]
        },
        {
          name: "出張費用書類",
          documents: [
            { path: "documents/出張費清算.xlsx", title: "出張費清算" },
          ]
        },

      ]
    }
  }
}
</script>
