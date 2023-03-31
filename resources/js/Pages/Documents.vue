<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-file-download</v-icon>
        書類
      </v-card-title>

      <v-card-text
          class="pt-0"
          v-for="category in categories" :key="category.id"
      >
        <div class="grey lighten-3 px-4 py-2">
          <h3>{{ category.name }}</h3>
        </div>

        <v-list>
          <div v-for="(document, index) in category.documents" :key="document.id">
            <v-divider v-if="index !== 0"></v-divider>

            <v-list-item two-line :href="$route('static', {path: document.path})">
              <v-list-item-icon>
                <v-icon>
                  mdi-file
                </v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-title>
                  {{ document["title"] }}
                </v-list-item-title>

                <v-list-item-subtitle v-if="document['description']">
                  {{ document["description"] }}
                </v-list-item-subtitle>
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
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  data() {
    return {
      categories: [
        {
          name: "総務用書類",
          documents: [
            {path: "documents/FAX送信表.doc", title: "FAX送信表", description: "2019/10/15更新"},
            {path: "documents/休職届.doc", title: "休職届", description: "2019/10/15更新"},
            {path: "documents/登録変更届.pdf", title: "登録変更届（pdf）", description: "2019/10/15更新"},
            {path: "documents/登録変更届.xlsx", title: "登録変更届（xlsx）", description: "2019/10/15更新"},
            {path: "documents/稟議書最新21.12月.doc", title: "稟議書", description: "2021/12/28更新"},
            {path: "documents/始末書マイナス.doc", title: "始末書（マイナス案件）.Word", description: "2020/1/14更新"},
            {path: "documents/始末書(マイナス).pdf", title: "始末書（マイナス案件）.pdf", description: "2020/1/14更新"},
            {path: "documents/始末書(その他).xls", title: "始末書（その他）xlsx", description: "2020/1/14更新"},
            {path: "documents/始末書(その他).pdf", title: "始末書（その他）pdf", description: "2020/1/14更新"},
            {path: "documents/休業・休職証明.pdf", title: "休職証明", description: "2022/9/22更新"},]
        },
        {
          name: "仮払用書類",
          documents: [
            {path: "documents/仮払申請書.pdf", title: "仮払申請書（pdf）", description: "2019/10/15更新"},
            {path: "documents/仮払申請書.xlsx", title: "仮払申請書（xlsx）", description: "2019/10/15更新"},
            {path: "documents/仮払清算.xlsx", title: "仮払清算", description: "2019/10/15更新"},
          ]
        },
        {
          name: "経費精算用書類",
          documents: [
            {path: "documents/経費清算書.pdf", title: "経費清算書（pdf）", description: "2019/10/15更新"},
            {path: "documents/経費清算書.xls", title: "経費精算書（xls）", description: "2019/10/15更新"},
          ]
        },
        {
          name: "出張費清算用書類",
          documents: [
            {path: "documents/出張費清算.xlsx", title: "出張費清算", description: "2019/10/15更新"},
          ]
        },
        {
          name: "副業届用書類",
          documents: [
            {path: "documents/副業申請書.doc", title: "副業申請書", description: "2020/3/15更新"},
          ]
        },
      ]
    }
  }
}
</script>
