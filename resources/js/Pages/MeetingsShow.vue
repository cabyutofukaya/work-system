<template>
  <Layout>
    <div>
      <v-card flat tile>
        <v-card-title>
          <v-icon dark left>mdi-file-document-edit</v-icon>
          議事録
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" sm="" :class="{ 'text-right': $vuetify.breakpoint.smAndUp }">
              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.commentVisitedUser)">
                閲覧者
                {{ Math.max(meeting["meeting_visitors_count"] - 1, 0) /* 閲覧者自身を除外するため一人減らす */ }}
              </v-chip>

              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.comment)">
                コメント
                {{ meeting["meeting_comments"].length }}
              </v-chip>


              <Button v-if="Number(meeting['user_id']) === Number($page.props.auth.user.id)"
                :small="$vuetify.breakpoint.xs" class="ml-2" :to="$route('meetings.edit', { meeting: meeting['id'] })">
                <v-icon left>
                  mdi-pencil
                </v-icon>
                編集
              </Button>

              <Button v-if="Number(meeting['user_id']) === Number($page.props.auth.user.id)"
                :small="$vuetify.breakpoint.xs" class="ml-2" @click.native="addFileModal">
                <v-icon left>
                  mdi-file
                </v-icon>
                ファイル添付
              </Button>
              
            </v-col>
          </v-row>

          <div class="description-list">
            <v-row>
              <v-col cols="12" sm="4">会議名</v-col>
              <v-col>
                {{ meeting.title }}
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">開催日時</v-col>
              <v-col>
                {{ meeting.started_at }}
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">作成者</v-col>
              <v-col>
                <div style="display: flex;">
                  <template v-if="!meeting.user['deleted_at']">
                    <Link :href="$route('users.show', { user: meeting['user_id'] })">
                    {{ meeting.user.name }}
                    </Link>

                    <v-card max-width="400" flat tile style="margin-top: -12px;">

                      <v-card-actions>
                        <v-list-item class="w-100">
                          <v-img :width="50" :height="50" cover :src="`/storage/user/${user.img_file}`"
                            v-if="user.img_file"></v-img>
                          <v-img :width="50" :height="50" cover src="/storage/user/noimg.jpeg"
                            v-if="!user.img_file"></v-img>

                        </v-list-item>
                      </v-card-actions>
                    </v-card>

                    <!-- <img style="width: 60px;height: 60px;margin-left: 20px;" :src="`/storage/user/${user.img_file}`"
                  v-if="user.img_file"/>
                <img style="width: 60px;height: 60px;margin-left: 20px;" src="/storage/user/noimg.jpeg" v-if="!user.img_file"/> -->


                  </template>
                  <template v-else>
                    {{ meeting.user.name }}

                  </template>
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">参加者</v-col>
              <v-col>
                <span style="white-space: pre-line;">{{ meeting.participants }}</span>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">議事内容</v-col>
              <v-col>
                <span style="white-space: pre-line;">{{ meeting.content }}</span>
              </v-col>
            </v-row>
          </div>


          <template v-if="images_list && !$vuetify.breakpoint.xs">
            <v-container fluid>
              <v-row>
                <v-col cols="3" v-for="(image, i_index) in images_list" :key="i_index">
                  <v-card class="mx-auto" max-width="400">

                    <div class="text-right" v-if="$page.props.auth.user.id == meeting.user_id">
                  <v-icon color="error" class="text-right" @click.native="deleteFile(i_index)">
                    mdi-close-circle-outline</v-icon>
                </div>

                    <v-img :src="`/storage/meeting/${image}`" max-height="250" min-height="150" cover
                      @click.native="clickImage(image)" color="surface-variant"></v-img>

                  </v-card>

                </v-col>
              </v-row>
            </v-container>
          </template>


          <template v-if="images_list && $vuetify.breakpoint.xs">
            <v-container fluid>
              <v-row>
                <v-col cols="12" v-for="(image, i_index) in images_list" :key="i_index">
                  <v-card>
                    <v-img :src="`/storage/meeting/${image}`" max-height="250" min-height="150"></v-img>
                  </v-card>
                </v-col>
              </v-row>
            </v-container>
          </template>

          <template v-if="files_list">
            <v-container fluid>
              <v-row>
                <v-col cols="12" v-for="(file, f_index) in files_list" :key="f_index" class="mb-3">

                  <v-row>

                    <a :href="`/storage/meeting/${file.name}`" target="_blank">
                      {{ file.tmp_name }}
                    </a>

                    <div v-if="$page.props.auth.user.id == meeting.user_id" class="ml-8">
                      <v-icon color="error" @click.native="deleteFile(f_index)">
                        mdi-close-circle-outline</v-icon>
                    </div>

                  </v-row>

                </v-col>
              </v-row>
            </v-container>
          </template>


          <v-row>

            <v-col class="text-right">
              <v-chip small class="mr-2" color="primary" :outlined="!meeting['has_own_like']" style="cursor:pointer"
                @click.native="meetingLikeChange(meeting['id'])">
                いいね {{ meeting['likes_count'] }}
                <v-icon small right dark>
                  mdi-thumb-up
                </v-icon>
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-card flat tile class="my-4" ref="commentVisitedUser">
        <v-card-title>
          閲覧者
        </v-card-title>

        <v-card-text class="mt-4">
          <v-row>
            <v-col cols="4" sm="2" v-for="user in users" :key="user['id']">
              <Link :href="$route('users.show', { user: user['id'] })">
              <span :class="{ 'visited-user': user['meeting_visitors_exists'] }">{{ user['name'] }}</span>
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
            <v-col cols="12" sm="" v-if="!meeting['meeting_comments'].length">
              まだコメントはありません
            </v-col>

            <v-col cols="12" sm="" class="text-right">
              <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="formMeetingCommentDialog = true">
              <v-icon left>
                mdi-plus
              </v-icon>
              コメントを書く
              </Link>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-text class="pt-0" v-if="meeting['meeting_comments'].length">
          <div v-for="meeting_comment in meeting['meeting_comments']" :key="meeting_comment.id">
            <v-divider class="my-4"></v-divider>

            <v-row>
              <v-col cols="12" sm="3">
                <div class="mb-2">
                  <h4>{{ meeting_comment.user.name }}</h4>
                  {{ meeting_comment['created_at'] }}
                </div>
              </v-col>

              <v-col cols="12" sm="7">
                <span style="white-space: pre-line;">{{ meeting_comment.comment }}</span>
              </v-col>

              <v-spacer></v-spacer>

              <v-col cols="12" sm="2" class="text-right"
                v-if="Number(meeting_comment['user_id']) === Number($page.props.auth.user.id)">
                <Link as="Button" :small="$vuetify.breakpoint.xs" class="ml-2" color="error" style="max-width:100%"
                  @click.native="meetingCommentDelete(meeting_comment.id)"
                  :loading="loading['meeting-comment-delete-' + meeting_comment.id]">
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
      <v-dialog v-model="formMeetingCommentDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
        @click:outside="resetMeetingCommentDialog">
        <v-card flat tile>
          <v-card-title>
            コメントを書く
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-list-item>
                <v-textarea dense filled prepend-inner-icon="mdi-pencil" v-model="formMeetingComment.comment"
                  :error="Boolean(formMeetingComment.errors.comment)"
                  :error-messages="formMeetingComment.errors.comment"></v-textarea>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Link as="Button" color="primary" @click.native="meetingCommentCreate"
              :loading="loading['meeting-comment-create']">
            <v-icon left>
              mdi-content-save-outline
            </v-icon>
            この内容で投稿する
            </Link>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Link as="Button" class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="resetMeetingCommentDialog">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
            </Link>
          </v-card-text>
        </v-card>
      </v-dialog>



      <!-- ファイル追加ダイアログ -->
      <v-dialog v-model="fileAddDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
        <v-card flat tile>
          <v-card-title>
            ファイルの追加
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-file-input chips prepend-icon="" prepend-inner-icon="mdi-paperclip" name="file_form" id="file_form"
                label="ファイルを選択する" accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"
                @change.native="uploadFile"></v-file-input>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Button :small="$vuetify.breakpoint.xs" color="primary" @click.native="updateMeetingFile"
              :loading="loading['update_file']">
              ファイルを追加する
            </Button>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="fileAddDialog = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </Button>
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
  name: 'MeetingsShow',
  props: ['meeting', 'users', 'user', 'images_list', 'files_list'],
  data() {
    return {
      meetingType: this.$route().params.meetingType ?? null,
      formMeetingCommentDialog: false,
      // コメント追加フォームヘ
      formMeetingComment: this.$inertia.form({
        meeting_id: this.meeting.id,
        comment: null,
      }),
      loading: {},

      file_name: undefined,
      fileDialog: false,

      fileAddDialog: false,
      file: [],

      formMeetingFileAdd: this.$inertia.form(`MeetingsFileAdd`, {
        name: undefined,
        type: undefined,
        tmp_name: undefined,
        id: this.$page['props']['meeting'].id,
      }),
    };
  },

  methods: {
    meetingCommentCreate() {
      this.formMeetingComment.post(this.$route('meeting-comments.store', { meeting: this.meeting.id }), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "meeting-comment-create", true),
        onSuccess: () => {
          this.$toasted.show('コメントを追加しました');

          this.resetMeetingCommentDialog();
        },
        onFinish: () => this.$set(this.loading, "meeting-comment-create", false),
      })
    },

    resetMeetingCommentDialog() {
      this.formMeetingCommentDialog = false;
      this.formMeetingComment.clearErrors();
      this.formMeetingComment.reset();
    },

    meetingCommentDelete($meetingCommentId) {
      this.$confirm('このコメントを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.formMeetingComment.delete(this.$route('meeting-comments.destroy', { meeting_comment: $meetingCommentId }), {
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "meeting-comment-delete-" + $meetingCommentId, true),
            onSuccess: () => this.$toasted.show('コメントを削除しました'),
            onFinish: () => this.$set(this.loading, "meeting-comment-delete-" + $meetingCommentId, false),
          })
        }
      })
    },

    // いいねトグル
    meetingLikeChange($meetingId) {
      this.$inertia.put(
        this.$route('meetings.like', { meeting: $meetingId }), {}, {
        preserveScroll: true,
      });
    },

    clickImage(file_name) {
      this.file_name = file_name;
      this.fileDialog = true;
    },

    addFileModal() {
      this.file = [];
      this.fileAddDialog = true;
    },

    uploadFile(e) {

      var formData = new FormData();

      var file = e.target.files[0];

      const config = {
        header: {
          "Content-Type": "multipart/form-data"
        }
      };


      //appendの第一引数がkey、第二引数がデータ
      formData.append("file", file);

      axios.post("/upload/meeting/file", formData, config).then(res => {
        console.log(res);
        console.log(res.data);
        console.log(res.data.name);

        console.log(res.data);

        this.formMeetingFileAdd.name = res.data.name;
        this.formMeetingFileAdd.type = res.data.type;
        this.formMeetingFileAdd.tmp_name = res.data.tmp_name;
        // this.imageContent[id] = res.data.name;


      }).catch(err => {
        alert('写真のアップに失敗しました。もう一度実施お願いします。');
      });
    },



    // ファイル更新
    updateMeetingFile: function () {


      this.formMeetingFileAdd.post(this.$route('meetings.update_file'), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "update_file", true),
        onSuccess: () => this.$toasted.show('ファイルを更新しました'),
        onFinish: () => this.$set(this.loading, "update_file", false),
      })
    },


    deleteFile: function (meeting_file) {

      this.$confirm('このファイルを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          //削除処理
          this.formMeetingFileAdd.post(this.$route('meetings-file.delete', { meeting: this.meeting.id, meeting_file: meeting_file }), {
            preserveState: (page) => Object.keys(page.props.errors).length,
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => this.$toasted.show('ファイルを削除しました'),
            onFinish: () => this.$set(this.loading, "delete", false),

          })
        }
      })
    },
  }
}
</script>
