<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-information</v-icon>
        お知らせ
      </v-card-title>

      <v-card-text v-if="Number(notice['user_id']) === Number($page.props.auth.user.id)">
        <v-row>
          <v-col class="text-right">
            <Button :small="$vuetify.breakpoint.xs" class="ml-2" @click.native="showDialog = true">
              <v-icon left>
                mdi-pencil
              </v-icon>
              編集
            </Button>

            <Button :small="$vuetify.breakpoint.xs" class="ml-2" color="error" @click.native="deleteNotice"
              :loading="loading['delete']">
              <v-icon left>
                mdi-delete-outline
              </v-icon>
              削除
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-text class="mt-4 pb-0">
        <h3>{{ notice.title }}</h3>
      </v-card-text>

      <v-card-text class="pb-0">
        <div class="mb-1">
          {{ notice["created_at"] }}<br>
        </div>
        <div style="display: flex;padding: 20px 0;">
          <template v-if="!notice['user']['deleted_at']">
            <Link :href="$route('users.show', { user: notice['user_id'] })">
            {{ notice["user"]["name"] }}
            </Link>

            <v-card max-width="400" style="margin-top: -12px;" flat tile>

              <v-card-actions>
                <v-list-item class="w-100">
                  <v-img :width="50" :height="50" cover :src="`/storage/user/${user.img_file}`"
                    v-if="user.img_file"></v-img>
                  <v-img :width="50" :height="50" cover src="/storage/user/noimg.jpeg" v-if="!user.img_file"></v-img>

                </v-list-item>
              </v-card-actions>
            </v-card>

            <!-- <img style="width: 60px;height: 60px;margin-left: 20px;margin-top: -14px;" :src="`/storage/user/${user.img_file}`"
                  v-if="user.img_file"/>
                <img style="width: 60px;height: 60px;margin-left: 20px;margin-top: -14px;" src="/storage/user/noimg.jpeg" v-if="!user.img_file"/> -->



          </template>
          <template v-else>
            {{ notice["user"]["name"] }}
          </template>
        </div>
      </v-card-text>

      <v-card-text>
        <span style="white-space: pre-line;">{{ notice["description"] }}</span>
      </v-card-text>

      <v-card-text>
        <div v-for="(notice_file) in notice['notice_files']" :key="notice_file.id">

          <v-card-text>
            <div class="notice-description-list">

              <v-row class="lighten-3 px-6">
                <a :href="`/storage/notice/${notice_file.path}`" :download="`${notice_file.name}`"
                  v-if="notice_file.type == 'file'">
                  <div class="text-right mt-2">
                    {{ notice_file.name }}
                  </div>
                </a>

                <a :href="`/storage/notice/${notice_file.path}`" :download="`${notice_file.name}`"
                  v-if="notice_file.type == 'image'">
                  <div class="text-right my-3">
                    <v-img :width="150" :height="120" cover :src="`/storage/notice/${notice_file.path}`"></v-img>
                  </div>
                </a>


                <v-icon class="ml-5 mb-a" size="x-large" color="error"
                  @click="deleteFile(notice_file.id)">mdi-delete-outline</v-icon>

              </v-row>
            </div>
          </v-card-text>
        </div>
      </v-card-text>

      <Button :small="$vuetify.breakpoint.xs" class="ml-2" @click.native="showDialogFile = true">
        <v-icon left>
          mdi-pencil
        </v-icon>
        ファイル追加
      </Button>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- お知らせ追加ダイアログ -->
    <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
      <v-card flat tile>
        <v-card-title>
          お知らせの編集
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
          <Button :small="$vuetify.breakpoint.xs" color="primary" @click.native="updateNotice"
            :loading="loading['update']">
            この内容で保存する
          </Button>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="showDialog = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- お知らせ追加ダイアログ -->
    <v-dialog v-model="showDialogFile" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
      <v-card flat tile>
        <v-card-title>
          ファイルの追加
        </v-card-title>

        <v-card-text>
          <v-list>
            <v-file-input chips prepend-icon="" prepend-inner-icon="mdi-paperclip" name="file_form" multiple
              id="file_form" label="ファイルを選択する" accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"
              v-model="formNoticeFileAdd.file"></v-file-input>
          </v-list>
        </v-card-text>

        <v-card-text class="text-center">
          <Button :small="$vuetify.breakpoint.xs" color="primary" @click.native="updateNoticeFile"
            :loading="loading['update_file']">
            ファイルを追加する
          </Button>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="showDialogFile = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>

  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ['user'],

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
        file: [],
        id:this.$page['props']['notice'].id,
      }),

      file_form: undefined,
    }
  },

  methods: {
    // お知らせ情報更新
    updateNotice: function () {

      this.updateForm.put(this.$route('notices.update', { notice: this.notice.id }), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "update", true),
        onSuccess: () => this.$toasted.show('お知らせの更新を完了しました'),
        onFinish: () => this.$set(this.loading, "update", false),
      })
    },

    // お知らせ情報削除
    deleteNotice: function () {
      this.$confirm('このお知らせを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('notices.destroy', { notice: this.notice.id }), {
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => this.$toasted.show('お知らせを削除しました'),
            onFinish: () => this.$set(this.loading, "delete", false),
          })
        }
      })
    },

    deleteFile: function (notice_file) {

 

      this.$confirm('このファイルを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          //削除処理
          this.formNoticeFile.post(this.$route('notices-file.delete', { notice: this.notice.id, notice_file: notice_file }), {
            preserveState: (page) => Object.keys(page.props.errors).length,
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => this.$toasted.show('ファイルを削除しました'),
            onFinish: () => this.$set(this.loading, "delete", false),

          })
        }
      })
    },

    // ファイル更新
    updateNoticeFile: function () {


      this.formNoticeFileAdd.post(this.$route('notices.update_file'), {
          preserveState: (page) => Object.keys(page.props.errors).length,
          onStart: () => this.$set(this.loading, "update_file", true),
          onSuccess: () => this.$toasted.show('ファイルを更新しました'),
          onFinish: () => this.$set(this.loading, "update_file", false),
        })
    },



  }
}
</script>
