<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-information</v-icon>
        お知らせ
      </v-card-title>

      <v-card-text class="py-0">
        <ul>
          <li>最近作成されたものから順に表示されます。</li>
        </ul>
      </v-card-text>

      <v-card-text>
        <v-row>
          <v-col class="text-right">
            <Button :small="$vuetify.breakpoint.xs" @click.native="showDialog = true">
              <v-icon left>
                mdi-plus
              </v-icon>
              お知らせを作成する
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4"></v-divider>

      <v-list>
        <div v-for="notice in notices['data']" :key="notice.id">
          <Link as="v-list-item" three-line :href="$route('notices.show', { id: notice.id })" dusk="noticeShow">
          <v-list-item-content>
            <v-list-item-title class="mb-1">{{ notice["created_at"] }}</v-list-item-title>

            <v-list-item-subtitle>
              <div class="mb-1">{{ notice["title"] }}</div>
              <div>{{ notice["user"]["name"] }}</div>
            </v-list-item-subtitle>
          </v-list-item-content>
          </Link>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <v-card-text v-if="!notices.length">
        お知らせはありません
      </v-card-text>

      <v-card-text>
        <v-pagination v-model="page" :length="length" @input="changePage"></v-pagination>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- お知らせ追加ダイアログ -->
    <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="form.clearErrors()">
      <v-card flat tile>
        <v-card-title>
          お知らせの追加
        </v-card-title>

        <v-card-text>
          <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="タイトル" name="title" v-model="form.title"
            maxlength="200" :error="Boolean(form.errors.title)" :error-messages="form.errors.title"></v-text-field>

          <v-textarea dense filled prepend-inner-icon="mdi-pencil" label="本文" name="description"
            v-model="form.description" :error="Boolean(form.errors.description)"
            :error-messages="form.errors.description"></v-textarea>


            <v-file-input chips prepend-icon="" multiple prepend-inner-icon="mdi-paperclip" name="file_name"
                id="file_name" label="ファイルを選択する" v-model="form.file_name"
                accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"></v-file-input>

        </v-card-text>

        <v-card-text class="text-center">
          <Button color="primary" :small="$vuetify.breakpoint.xs" @click.native="create" :loading="loading['create']">
            <v-icon left>
              mdi-content-save-edit-outline
            </v-icon>
            この内容で追加する
          </Button>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="form.clearErrors(); showDialog = false">
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

  props: ['notices'],

  data() {
    return {
      page: Number(this.notices['current_page']),
      length: Number(this.notices['last_page']),
   


      showDialog: false,
      form: this.$inertia.form({
        title: null,
        description: null,
        file_name: undefined,
      }),

      loading: {},
    }
  },

  methods: {
    // お知らせ情報作成
    create: function () {
      this.form.post(this.$route('notices.store'), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('お知らせの作成を完了しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      })
    },

    changePage() {
      this.$inertia.get(this.$route('notices.index'), {
        page: this.page,
      });
    },
  }
}
</script>
