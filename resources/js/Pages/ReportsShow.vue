<template>
  <Layout>
    <div>
      <v-card flat tile>
        <v-card-title>
          <v-icon dark left>mdi-notebook</v-icon>
          日報
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" sm="" class="text-h6">
              <div style="display: flex;">
                
              {{ report.date }}
              <template v-if="!report.user.deleted_at">
                <Link :href="$route('users.show', { user: report['user_id'] })">
                {{ report.user.name }}
                </Link>

                <img style="width: 60px;height: 60px;margin-left: 20px;margin-top: -14px;" :src="`/storage/user/${user.img_file}`"
                  v-if="user.img_file"/>
                <img style="width: 60px;height: 60px;margin-left: 20px;margin-top: -14px;" src="/storage/user/noimg.jpeg" v-if="!user.img_file"/>

              </template>
              <template v-else>
                {{ report.user.name }}

      
              </template>
            </div>
            </v-col>

            <v-col cols="12" sm="" :class="{ 'text-right': $vuetify.breakpoint.smAndUp }">
              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.commentVisitedUser)">
                閲覧者
                {{ Math.max(report["report_visitors_count"] - 1, 0) /* 閲覧者自身を除外するため一人減らす */ }}
              </v-chip>

              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.comment)">
                コメント
                {{ report["report_comments"].length }}
              </v-chip>

              <Button v-if="Number(report['user_id']) === Number($page.props.auth.user.id)"
                :small="$vuetify.breakpoint.xs" class="ml-2" :to="$route('reports.edit', { report: report['id'] })">
                <v-icon left>
                  mdi-pencil
                </v-icon>
                編集
              </Button>
            </v-col>
          </v-row>
        </v-card-text>

        <div v-for="report_content in report['report_contents']" :key="report_content.id">
          <template v-if="report_content.type === 'work'">
            <v-card-text>
              <div class="report-description-list">
                <v-row class="grey lighten-3 px-6">
                  <v-col>
                    <h3>{{ report_content["type_name"] }}</h3>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" sm="3">
                    <h4 class="mb-1">仕事内容</h4>
                    {{ report_content.title }}
                  </v-col>

                  <v-col cols="12" sm="">
                    <h4 class="mb-1">
                      本文
                      <v-chip x-small color="error" class="ml-2" v-if="report_content['is_complaint']">
                        クレーム・トラブル
                      </v-chip>

                      <v-chip x-small color="#a9d6fc" class="ml-2" v-if="report_content['is_zaitaku']">
                        在宅
                      </v-chip>

                    </h4>
                    <span style="white-space: pre-line;">{{ report_content.description }}</span>


                    <div class="text-right mt-2">
                      <v-chip small class="mr-2" color="primary" :outlined="!report_content['has_own_like']"
                        style="cursor:pointer" @click.native="reportContentLikeChange(report_content['id'])">
                        いいね {{ report_content['likes_count'] }}
                        <v-icon small right dark>
                          mdi-thumb-up
                        </v-icon>
                      </v-chip>
                    </div>


                    <!-- <div v-if="report_content.file_name">
                      <a :href="`/storage/report/${report_content.file}`" download
                        v-if="extension_list.includes(report_content.file_name.split('.').pop())">
                        <div class="text-right mt-2" v-if="report_content.file_name">
                          {{ report_content.file_name }}
                        </div>
                      </a>

                      <v-img :width="50" :height="50" cover :src="`/storage/report/${report_content.file}`"
                        v-if="!extension_list.includes(report_content.file_name.split('.').pop())"></v-img>

                    </div> -->


                  </v-col>

                </v-row>
              </div>
            </v-card-text>
          </template>

          <template v-if="report_content.type === 'sales'">
            <v-card-text>
              <div class="report-description-list">
                <v-row class="grey lighten-3 px-6">
                  <v-col>
                    <h3>{{ report_content["type_name"] }}</h3>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" sm="3">
                    <h4 class="mb-1">会社</h4>
                    <div class="d-flex align-center">
                      <div>
                        <Link :href="$route('clients.show', { client: report_content.client.id })">
                        <v-list-item-avatar tile>
                          <v-img :src="report_content.client['icon_image_url']" alt=""></v-img>
                        </v-list-item-avatar>
                        </Link>
                      </div>

                      <div>
                        <div>
                          <Link :href="$route('clients.show', { client: report_content.client.id })">
                          {{ report_content.client.name }}
                          </Link>
                        </div>
                        <div class="mt-1" v-if="report_content.branch">
                          {{ report_content.branch.name }}
                        </div>
                        <div class="mt-1">
                          {{ report_content["client"]["client_type_name"] }}
                        </div>
                      </div>
                    </div>

                    <h4 class="mt-2 mb-1">面談者</h4>
                    {{ report_content["participants"] }}

                    <h4 class="mt-2 mb-1">営業手段</h4>
                    {{ report_content["sales_method"]["name"] }}

                    <template v-if="report_content['products'].length">
                      <h4 class="mt-2 mb-1">商材の評価</h4>
                      <div v-for="(product, index) in report_content['products']" :key="index">
                        <EvaluationIcon :evaluation="product['pivot']['evaluation']['grade']"></EvaluationIcon>
                        {{ product.name }}
                      </div>
                    </template>

                    <h4 class="mt-2 mb-1" v-if='report_content["product_description"]'>商材評価の備考欄</h4>
                    {{ report_content["product_description"] }}


                  </v-col>

                  <v-col cols="12" sm="">
                    <h4 class="mb-1">
                      面談内容
                      <v-chip x-small color="error" class="ml-2" v-if="report_content['is_complaint']">
                        クレーム・トラブル
                      </v-chip>
                    </h4>
                    <span style="white-space: pre-line;">{{ report_content.description }}</span>


                    <div class="text-right mt-2">
                      <v-chip small class="mr-2" color="primary" :outlined="!report_content['has_own_like']"
                        style="cursor:pointer" @click.native="reportContentLikeChange(report_content['id'])">
                        いいね {{ report_content['likes_count'] }}
                        <v-icon small right dark>
                          mdi-thumb-up
                        </v-icon>
                      </v-chip>
                    </div>

                    <!-- <div v-if="report_content.file_name">
                      <a :href="`/storage/report/${report_content.file}`" download
                        v-if="extension_list.includes(report_content.file_name.split('.').pop())">
                        <div class="text-right mt-2" v-if="report_content.file_name">
                          {{ report_content.file_name }}
                        </div>
                      </a>

                      <div class="text-right mt-2"
                        v-if="!extension_list.includes(report_content.file_name.split('.').pop())">
                        <v-img :width="200" :height="150" cover :src="`/storage/report/${report_content.file}`"></v-img>
                      </div>

                    </div> -->


                  </v-col>


                </v-row>
              </div>
            </v-card-text>
          </template>

        </div>


        <div v-for="(report_file, index) in report['report_files']" :key="report_file.id">
          <v-card-text>
            <div class="report-description-list">

              <v-row class="grey lighten-3 px-6" v-if="index == 0">
                <v-col>
                  <h3>
                    <template>ファイル</template>
                  </h3>
                </v-col>
              </v-row>

              <v-row class="lighten-3 px-6">
                <a :href="`/storage/report/${report_file.path}`" download v-if="report_file.type == 'file'">
                  <div class="text-right mt-2">
                    {{ report_file.name }}
                  </div>
                </a>

                <a :href="`/storage/report/${report_file.path}`" download v-if="report_file.type == 'image'">
                  <div class="text-right my-3">
                    <v-img :width="150" :height="120" cover :src="`/storage/report/${report_file.path}`"></v-img>
                  </div>
                </a>

                <!-- <v-img :width="200" :height="150" cover :src="`/storage/report/${report_file.path}`"
                  v-if="report_file.type == 'image'"></v-img> -->
              </v-row>
            </div>
          </v-card-text>
        </div>



      </v-card>

      <v-card flat tile class="my-4" ref="commentVisitedUser">
        <v-card-title>
          閲覧者
        </v-card-title>

        <v-card-text class="mt-4">
          <v-row>
            <v-col cols="4" sm="2" v-for="user in users" :key="user['id']">
              <Link :href="$route('users.show', { user: user['id'] })">
              <span :class="{ 'visited-user': user['report_visitors_exists'] }">{{ user['name'] }}</span>
              </Link>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-card flat tile class="my-4" ref="comment">
        <v-card-title>
          コメント
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" sm="" v-if="!report['report_comments'].length">
              まだコメントはありません
            </v-col>

            <v-col cols="12" sm="" class="text-right">
              <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="formReportCommentDialog = true">
              <v-icon left>
                mdi-plus
              </v-icon>
              コメントを書く
              </Link>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-text class="pt-0" v-if="report['report_comments'].length">
          <div v-for="report_comment in report['report_comments']" :key="report_comment.id">
            <v-divider class="my-4"></v-divider>

            <v-row :class="{ 'grey': report_comment.is_readed == 1, 'lighten-4': report_comment.is_readed == 1 }">
              <v-col cols="12" sm="3">
                <div class="mb-2">
                  <h4>{{ report_comment.user.name }}</h4>
                  {{ report_comment['created_at'] }}
                </div>
              </v-col>

              <v-col cols="12" sm="7">
                <span
                  style="white-space: pre-line;font-size: smaller;color:rgb(30, 132, 180); background-color: rgb(240, 217, 101);"
                  v-if="report_comment.mention_name">@{{
                    report_comment.mention_name }}<br /></span>
                <span style="white-space: pre-line;">{{ report_comment.comment }}</span>
              </v-col>

              <v-spacer></v-spacer>

              <v-col cols="12" sm="2" class="text-right">

                <!-- v-if="Number(report_comment['user_id']) === Number($page.props.auth.user.id) || report_comment.is_readed == 0 && Number(report_comment['mention_id']) === Number($page.props.auth.user.id)" -->
                <div v-if="report_comment['is_readed'] == 0">
                  <Link as="Button" color="primary" dark class="ml-2" :small="$vuetify.breakpoint.xs"
                    style="max-width:100%"
                    v-if="(report_comment['mention_id'] == null && Number(report_comment['user_id']) == Number($page.props.report.user_id)) || (report_comment['mention_id'] != null && (Number(report_comment['mention_id']) === Number($page.props.auth.user.id)))"
                    @click.native.prevent="isReadedComment(report_comment.id)"
                    :loading="loading['report-comment-update-' + report_comment.id]" dusk="reportCommentUpdate">
                  <v-icon left>
                    mdi-checkbox-marked-outline
                  </v-icon>
                  既読
                  </Link>
                </div>
                <!-- v-if="report_comment.is_readed == 0 && (Number(report_comment['mention_id']) === Number($page.props.auth.user.id)) || !(report_comment['mention_id'])" -->



                <Link as="Button" :small="$vuetify.breakpoint.xs" class="ml-2" color="error" style="max-width:100%"
                  v-if="Number(report_comment['user_id']) == Number($page.props.auth.user.id)"
                  @click.native="reportCommentDelete(report_comment.id)"
                  :loading="loading['report-comment-delete-' + report_comment.id]">
                <v-icon left>
                  mdi-delete-outline
                </v-icon>
                削除
                </Link>
              </v-col>
            </v-row>
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <BackButton></BackButton>
        </v-card-text>
      </v-card>

      <!-- コメント入力ダイアログ -->
      <v-dialog v-model="formReportCommentDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
        @click:outside="resetReportCommentDialog">
        <v-card flat tile>
          <v-card-title>
            コメントを書く
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-list-item>
                <v-textarea dense filled prepend-inner-icon="mdi-pencil" v-model="formReportComment.comment"
                  :error="Boolean(formReportComment.errors.comment)"
                  :error-messages="formReportComment.errors.comment"></v-textarea>
              </v-list-item>

              <v-list-item>
                <v-autocomplete dense hint="メンションする場合は、選択してください" persistent-hint name="mention"
                  v-model="formReportComment.mention_id" :items="mentions" item-value="id"
                  item-text="name"></v-autocomplete>
              </v-list-item>

            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Link as="Button" color="primary" @click.native="reportCommentCreate"
              :loading="loading['report-comment-create']">
            <v-icon left>
              mdi-content-save-outline
            </v-icon>
            この内容で投稿する
            </Link>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Link as="Button" class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="resetReportCommentDialog">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
            </Link>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
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

  props: ['report', 'users', 'mentions', 'mentions', 'user'],


  data() {
    return {
      reportType: this.$route().params.reportType ?? null,
      formReportCommentDialog: false,
      // コメント追加フォームヘ
      formReportComment: this.$inertia.form({
        report_id: this.report.id,
        comment: null,
        mention_id: null,
      }),
      loading: {},
      extension_list: ['csv', 'txt', 'pdf', 'xlsx', 'xlsm'],
    };
  },

  methods: {
    reportCommentCreate() {
      this.formReportComment.post(this.$route('report-comments.store', { report: this.report.id }), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "report-comment-create", true),
        onSuccess: () => {
          this.$toasted.show('コメントを追加しました');

          this.resetReportCommentDialog();
        },
        onFinish: () => this.$set(this.loading, "report-comment-create", false),
      })
    },

    resetReportCommentDialog() {
      this.formReportCommentDialog = false;
      this.formReportComment.clearErrors();
      this.formReportComment.reset();
    },

    reportCommentDelete($reportCommentId) {
      this.$confirm('このコメントを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.formReportComment.delete(this.$route('report-comments.destroy', { report_comment: $reportCommentId }), {
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "report-comment-delete-" + $reportCommentId, true),
            onSuccess: () => this.$toasted.show('コメントを削除しました'),
            onFinish: () => this.$set(this.loading, "report-comment-delete-" + $reportCommentId, false),
          })
        }
      })
    },

    // いいねトグル
    reportContentLikeChange($reportContentId) {
      this.$inertia.put(
        this.$route('report-contents.like', { report_content: $reportContentId }), {}, {
        preserveScroll: true,
      });
    },

    // ToDo対応済み
    isReadedComment($reportCommentId) {
      this.$confirm(
        "このコメントを既読済みにしてよろしいですか？",
        { color: 'info' }
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('report-comments.complete', { report_comment: $reportCommentId }), {}, {
            // only: ['report_commnet'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "report-comment-update-" + $reportCommentId, true),
            onSuccess: () => this.$toasted.show('既読済みにしました'),
            onFinish: () => this.$set(this.loading, "report-comment-update-" + $reportCommentId, false),
          })
        }
      })
    },

  }
}
</script>
