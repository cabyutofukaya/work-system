<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-notebook</v-icon>
        日報添付ファイルの編集
      </v-card-title>
    </v-card>

    <!-- 日付 -->
    <v-card-text class="mt-4 pb-0">
      <div class=report-description-list>

        <v-row>
          <v-col cols="12" sm="4" class="align-self-center">
            <h4>ファイル</h4>
          </v-col>

          <v-col>
            <v-file-input chips prepend-icon="" prepend-inner-icon="mdi-paperclip" name="file_name" multiple
              id="file_name" label="ファイルを選択する" accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm" v-model="form.file_name"></v-file-input>
          </v-col>
        </v-row>

      </div>
    </v-card-text>

    <!-- 日報作成ボタン -->
    <v-card-text class="text-center">
      <Button center color="primary" :small="$vuetify.breakpoint.xs" @click.native="create" :loading="loading['create']">
        <v-icon left>
          mdi-content-save-edit-outline
        </v-icon>
        このファイルを追加する
      </Button>
    </v-card-text>

  </Layout>
</template>

<style scoped>
.visited-user {
  background: linear-gradient(transparent 60%, #00ccff 60%);
}
</style>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ['report'],


  data() {
    return {
      form: this.$inertia.form(`ReportsFileAdd${this.report.id}`, {
        file_name: null,
        test: 'yy',
      }),
      loading: {},
    };
  },

  methods: {
    create: function () {

      this.form.post(this.$route('reports.file.store', {report: this.report.id}), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('ファイル追加しました'),
        onError: errors => {
          // バリデーションエラーをトースト表示する
          // フロント側チェックを行っているため発生しない前提
          this.$toasted.error(Object.values(errors).join("\n"), {
            duration: 1000 * 60 * 10,
            type: 'error'
          });
        },
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
