<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-file-document-edit</v-icon>
          議事録
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" sm="" :class="{'text-right': $vuetify.breakpoint.smAndUp}">
              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.commentVisitedUser)">
                閲覧者
                {{ Math.max(meeting["meeting_visitors_count"] - 1, 0) /* 閲覧者自身を除外するため一人減らす */ }}
              </v-chip>

              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.comment)">
                コメント
                {{ meeting["meeting_comments"].length }}
              </v-chip>


              <Button
                  v-if="Number(meeting['user_id']) === Number($page.props.auth.user.id)"
                  :small="$vuetify.breakpoint.xs"
                  class="ml-2"
                  :to="$route('meetings.edit',{meeting : meeting['id']})"
              >
                <v-icon left>
                  mdi-pencil
                </v-icon>
                編集
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
                <template v-if="!meeting.user['deleted_at']">
                  <Link :href="$route('users.show', {user: meeting['user_id']})">
                    {{ meeting.user.name }}
                  </Link>
                </template>
                <template v-else>
                  {{ meeting.user.name }}
                </template>
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

          <v-row>

            <v-col class="text-right">
              <v-chip
                  small class="mr-2"
                  color="primary"
                  :outlined="!meeting['has_own_like']"
                  style="cursor:pointer"
                  @click.native="meetingLikeChange(meeting['id'])"
              >
                いいね {{ meeting['likes_count'] }}
                <v-icon small right dark>
                  mdi-thumb-up
                </v-icon>
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-card
          flat tile
          class="my-4"
          ref="commentVisitedUser"
      >
        <v-card-title>
          閲覧者
        </v-card-title>

        <v-card-text class="mt-4">
          <v-row>
            <v-col cols="4" sm="2" v-for="user in users" :key="user['id']">
              <Link :href="$route('users.show', {user: user['id']})">
                <span :class="{'visited-user' : user['meeting_visitors_exists']}">{{ user['name'] }}</span>
              </Link>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-card
          flat tile
          class="my-4"
          ref="comment"
      >
        <v-card-title>
          コメント
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" sm="" v-if="!meeting['meeting_comments'].length">
              まだコメントはありません
            </v-col>

            <v-col cols="12" sm="" class="text-right">
              <Link as="Button"
                    :small="$vuetify.breakpoint.xs"
                    @click.native="formMeetingCommentDialog = true"
              >
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

              <v-col
                  cols="12" sm="2"
                  class="text-right"
                  v-if="Number(meeting_comment['user_id']) === Number($page.props.auth.user.id)"
              >
                <Link as="Button"
                      :small="$vuetify.breakpoint.xs"
                      class="ml-2"
                      color="error"
                      style="max-width:100%"
                      @click.native="meetingCommentDelete(meeting_comment.id)"
                      :loading="loading['meeting-comment-delete-' + meeting_comment.id]"
                >
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
      <v-dialog
          v-model="formMeetingCommentDialog"
          :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
          @click:outside="resetMeetingCommentDialog"
      >
        <v-card flat tile>
          <v-card-title>
            コメントを書く
          </v-card-title>

          <v-card-text>
            <v-list>
              <v-list-item>
                <v-textarea
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    v-model="formMeetingComment.comment"
                    :error="Boolean(formMeetingComment.errors.comment)"
                    :error-messages="formMeetingComment.errors.comment"
                ></v-textarea>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Link as="Button"
                  color="primary"
                  @click.native="meetingCommentCreate"
                  :loading="loading['meeting-comment-create']"
            >
              <v-icon left>
                mdi-content-save-outline
              </v-icon>
              この内容で投稿する
            </Link>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Link as="Button"
                  class="mt-4"
                  :small="$vuetify.breakpoint.xs"
                  @click.native="resetMeetingCommentDialog"
            >
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
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},
  name: 'MeetingsShow',
  props: ['meeting', 'users'],
  data() {
    return {
      meetingType: this.$route().params.meetingType ?? null,
      formMeetingCommentDialog: false,
      // コメント追加フォームヘ
      formMeetingComment: this.$inertia.form({
        meeting_id: this.meeting.id,
        comment: null,
      }),
      loading: {}
    };
  },

  methods: {
    meetingCommentCreate() {
      this.formMeetingComment.post(this.$route('meeting-comments.store', {meeting: this.meeting.id}), {
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
          this.formMeetingComment.delete(this.$route('meeting-comments.destroy', {meeting_comment: $meetingCommentId}), {
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
          this.$route('meetings.like', {meeting: $meetingId}), {}, {
            preserveScroll: true,
          });
    },
  }
}
</script>
