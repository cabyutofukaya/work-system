<template>
  <Layout>
    <v-container>
      <h3 class="mb-4">社内連絡事項詳細</h3>

      <v-card elevation="2" class="pa-6">
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="8">
            <h3 class="font-weight-bold mb-2">{{ notice.title }}</h3>
            <div class="text-caption grey--text mb-1">
              {{ formatDate(notice.created_at) }}
            </div>
            <div class="mb-4">
              <v-icon small class="mr-1" color="primary">mdi-account</v-icon>
              <a :href="`/users/${notice.user.id}`" class="text--primary text-decoration-underline">
                {{ notice.user.name }}
              </a>
            </div>
          </v-col>

          <!-- 操作ボタン -->
          <v-col cols="12" md="4" class="text-md-right text-left">
            <v-btn color="primary" class="ma-1" small elevation="1" @click.native="showDialogFile = true">
              <v-icon left>mdi-paperclip</v-icon> 添付
            </v-btn>
            <v-btn color="info" class="ma-1" small elevation="1" @click.native="showDialog = true">
              <v-icon left>mdi-pencil</v-icon> 編集
            </v-btn>
            <v-btn color="error" class="ma-1" small elevation="1" @click.native="deleteNotice">
              <v-icon left>mdi-delete</v-icon> 削除
            </v-btn>
            <v-chip small color="grey lighten-3" class="mt-2">
              閲覧者 {{ notice.visitor_count }}
            </v-chip>
          </v-col>
        </v-row>

        <v-divider class="my-4"></v-divider>

        <!-- 本文 -->
        <v-row>
          <v-col cols="12">
            <div class="body-1" style="white-space: pre-wrap;">
              {{ notice.description }}
            </div>
          </v-col>
        </v-row>
      </v-card>

      <!-- 社内連絡事項追加ダイアログ -->
      <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
        <v-card flat tile>
          <v-card-title>
            社内連絡事項の編集
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="タイトル" name="title"
                v-model="updateForm.title" maxlength="200" :error="Boolean(updateForm.errors.title)"
                :error-messages="updateForm.errors.title"></v-text-field>

              <v-textarea dense filled prepend-inner-icon="mdi-pencil" label="本文" name="description"
                v-model="updateForm.description" :error="Boolean(updateForm.errors.description)"
                :error-messages="updateForm.errors.description"></v-textarea>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <v-btn :small="$vuetify.breakpoint.xs" color="primary" @click.native="updateNotice"
              :loading="loading['update']">
              この内容で保存する
            </v-btn>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <v-btn class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="showDialog = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>

      <!-- 社内連絡事項追加ダイアログ -->
      <v-dialog v-model="showDialogFile" :max-width="$vuetify.breakpoint.smAndUp ? '400px' : 'unset'">
        <v-card flat tile>
          <v-card-title>
            ファイルの追加
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-file-input v-model="formNoticeFileAdd.file" label="ファイルを選択"
                accept="image/*,.pdf,.csv,.txt,.xlsx,.xlsm" />
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <v-btn :small="$vuetify.breakpoint.xs" color="primary" @click.native="updateNoticeFile"
              :loading="loading['update_file']">
              ファイルを追加する
            </v-btn>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <v-btn class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="showDialogFile = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>


      <v-dialog v-model="fileDialog" :max-width="$vuetify.breakpoint.smAndUp ? '800px' : 'unset'"
        @click:outside="fileDialog = false;">
        <v-card flat tile>

          <v-img :src="`/storage/notice/${file_name}`"></v-img>

        </v-card>
      </v-dialog>
    </v-container>

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
import axios from 'axios'


export default {
  components: { Layout, Link },

  props: ['user', 'login_user', 'images_list', 'files_list', 'users'],

  data() {

    return {
      notice: this.$page['props']['notice'],
      showDialog: false,
      showDialogFile: false,
      updateForm: this.$inertia.form(JSON.parse(JSON.stringify(this.$page['props']['notice']))),
      loading: {},
      formNoticeFile: this.$inertia.form(`NoticesEdit`, {
        file: [],
      }),

      formNoticeFileAdd: this.$inertia.form(`NoticesFileAdd`, {
        file: null,
        id: this.$page.props.notice.id,
      }),

      file_form: undefined,

      file_name: undefined,
      fileDialog: false,
    }
  },

  methods: {
    // 社内連絡事項情報更新
    updateNotice: function () {

      this.updateForm.put(this.$route('notices.update', { notice: this.notice.id }), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "update", true),
        onSuccess: () => this.$toasted.show('社内連絡事項の更新を完了しました'),
        onFinish: () => this.$set(this.loading, "update", false),
      })
    },

    // 社内連絡事項情報削除
    deleteNotice: function () {
      this.$confirm('この社内連絡事項を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('notices.destroy', { notice: this.notice.id }), {
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => this.$toasted.show('社内連絡事項を削除しました'),
            onFinish: () => this.$set(this.loading, "delete", false),
          })
        }
      })
    },


    clickImage(file_name) {
      this.file_name = file_name;
      this.fileDialog = true;
    },

    async deleteFile(notice_file) {
      const isAccept = await this.$confirm('このファイルを削除してよろしいですか？<br>削除した項目を元に戻すことはできません')

      if (!isAccept) return;

      try {
        await axios.post('/api/notices-file/delete', {
          notice: this.notice.id,
          notice_file: notice_file.id,
        }, {
          withCredentials: true,
        });

        this.$toasted.show('ファイルを削除しました');
        this.$inertia.reload({ only: ['notice'] })
        this.$nextTick(() => {
          this.notice = this.$page.props.notice
        })

      } catch (error) {
        console.error('削除失敗', error)
        this.$toasted.error('ファイルの削除に失敗しました')
      }
    },


    async updateNoticeFile() {
      const formData = new FormData();
      formData.append('file', this.formNoticeFileAdd.file);
      formData.append('id', this.formNoticeFileAdd.id);

      axios.post(`/api/notices-file/${this.notice.id}/file`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        withCredentials: true,
      }).then((response) => {
        console.log('アップロード成功:', response.data.file)
        this.showDialogFile = false;
        this.formNoticeFileAdd.file = null;
        this.$toasted.show(response.data.message);
        this.$inertia.reload({ only: ['notice'] })
        this.$nextTick(() => {
          this.notice = this.$page.props.notice
        })
      })
    },

    formatDate(datetime) {
      const date = new Date(datetime);
      return date.toLocaleString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
  }
}


</script>
