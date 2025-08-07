<template>
  <Layout>
    <v-container>
      <!-- タイトル -->
      <h3 class="mb-4">運転手マスタ</h3>

      <!-- 検索フォーム -->
      <v-card outlined>
        <v-card-text>
          <v-row dense>
            <v-col cols="2">
              <v-text-field label="運送会社コード" v-model="searchForm.companyCode" dense></v-text-field>
            </v-col>
            <v-col cols="2">
              <v-text-field label="運転手ID" v-model="searchForm.driverId" dense></v-text-field>
            </v-col>
            <v-col cols="2">
              <v-text-field label="運転手名" v-model="searchForm.driverName" dense></v-text-field>
            </v-col>
            <v-col cols="2">
              <v-text-field label="ひらがな" v-model="searchForm.kana" dense></v-text-field>
            </v-col>
            <v-col cols="2">
              <v-text-field label="運送会社名" v-model="searchForm.companyName" dense></v-text-field>
            </v-col>
            <v-col cols="2" class="d-flex align-center">
              <v-btn color="success" @click="searchDrivers">絞込検索</v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- 検索結果 -->
      <h3 class="my-4">検索結果</h3>

      <v-btn color="primary" class="mb-2">新規登録</v-btn>

      <v-data-table :headers="headers" :items="drivers" class="elevation-1" disable-pagination hide-default-footer>
        <template v-slot:item.actions="{ item }">
          <v-btn color="success" small class="mr-2">編集</v-btn>
          <v-btn color="error" small>削除</v-btn>
        </template>
      </v-data-table>
    </v-container>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  data() {
    return {
      searchForm: {
        companyCode: '',
        driverId: '',
        driverName: '',
        kana: '',
        companyName: '',
      },
      headers: [
        { text: '操作', value: 'actions', sortable: false },
        { text: '運送会社コード', value: 'companyCode' },
        { text: '運転手ID', value: 'driverId' },
        { text: '運転手名', value: 'driverName' },
        { text: 'ひらがな', value: 'kana' },
        { text: '運送会社名', value: 'companyName' },
      ],
      drivers: [
        {
          companyCode: '0000002',
          driverId: '1',
          driverName: '佐藤 一郎',
          kana: 'さとう いちろう',
          companyName: '正運輸（株）',
        },
        {
          companyCode: '0000002',
          driverId: '2',
          driverName: '山田 太郎',
          kana: 'やまだ たろう',
          companyName: '正運輸（株）',
        },
      ],
    };
  },
  methods: {
    searchDrivers() {
      // 検索処理
      console.log('検索実行', this.searchForm);
    },
  },
};
</script>
