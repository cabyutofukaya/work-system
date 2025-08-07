<template>
  <Layout>
    <div>
      <!-- 会議記録カード -->
      <v-card flat tile>
        <v-card-title>
          <v-icon dark left>mdi-file-document-edit</v-icon>
          会議記録
        </v-card-title>

        <v-card-text>
          <!-- ボタン・チップ -->
          <v-row>
            <v-col cols="12" :class="{ 'text-right': $vuetify.breakpoint.smAndUp }">
              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.commentVisitedUser)">
                既読済メンバー {{ Math.max(meeting.meeting_visitors_count - 1, 0) }}
              </v-chip>

              <v-chip small class="mr-2" @click="$vuetify.goTo($refs.comment)">
                メモ {{ meeting.meeting_comments.length }}
              </v-chip>

              <template v-if="isOwner">
                <v-btn :small="$vuetify.breakpoint.xs" class="ml-2"
                  :to="$route('meetings.edit', { meeting: meeting.id })">
                  <v-icon left>mdi-pencil</v-icon> 編集
                </v-btn>
                <v-btn :small="$vuetify.breakpoint.xs" class="ml-2" @click.native="addFileModal">
                  <v-icon left>mdi-file</v-icon> ファイル添付
                </v-btn>
              </template>
            </v-col>
          </v-row>

          <!-- 会議記録詳細 -->
          <div class="description-list">
            <InfoRow label="会議名" :value="meeting.title" />
            <InfoRow label="開催日時" :value="meeting.started_at" />
            <InfoRow label="作成者">
              <template v-if="!meeting.user.deleted_at">
                <Link :href="$route('users.show', { user: meeting.user_id })">{{ meeting.user.name }}</Link>
              </template>
              <template v-else>{{ meeting.user.name }}</template>
            </InfoRow>
            <InfoRow label="参加者" :value="meeting.participants" multiline />
            <InfoRow label="議事内容" :value="meeting.content" multiline />
          </div>

          <!-- いいね -->
          <v-row>
            <v-col class="text-right">
              <v-chip small class="mr-2" color="primary" :outlined="!meeting.has_own_like" style="cursor:pointer"
                @click.native="meetingLikeChange(meeting.id)">
                いいね {{ meeting.likes_count }}
                <v-icon small right dark>mdi-thumb-up</v-icon>
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- 既読済みメンバー -->
      <v-card flat tile class="my-4" ref="commentVisitedUser">
        <v-card-title>既読済メンバー</v-card-title>
        <v-card-text class="mt-4">
          <v-row>
            <v-col cols="4" sm="2" v-for="user in users" :key="user.id">
              <Link :href="$route('users.show', { user: user.id })">
              <span :class="{ 'visited-user': user.meeting_visitors_exists }">{{ user.name }}</span>
              </Link>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- メモ一覧 -->
      <v-card flat tile class="my-4" ref="comment">
        <v-card-title>メモ</v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="12" v-if="!meeting.meeting_comments.length">まだメモはありません</v-col>
            <v-col cols="12" class="text-right">
              <v-btn :small="$vuetify.breakpoint.xs" @click.native="formMeetingCommentDialog = true">
                <v-icon left>mdi-plus</v-icon> メモを書く
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>

        <!-- メモ一覧 -->
        <v-card-text class="pt-0" v-if="meeting.meeting_comments.length">
          <v-card-text class="pt-0" v-if="meeting.meeting_comments.length">
            <div v-for="comment in meeting.meeting_comments" :key="comment.id">
              <v-divider class="my-4" />
              <v-row>
                <v-col cols="12" sm="3">
                  <div class="mb-2">
                    <h4>{{ comment.user.name }}</h4>
                    {{ comment.created_at }}
                  </div>
                </v-col>
                <v-col cols="12" sm="7">
                  <span style="white-space: pre-line;">{{ comment.comment }}</span>
                </v-col>
                <v-col cols="12" sm="2" class="text-right" v-if="isCommentOwner(comment.user_id)">
                  <v-btn :small="$vuetify.breakpoint.xs" class="ml-2" color="error" style="max-width: 100%"
                    @click.native="meetingCommentDelete(comment.id)"
                    :loading="loading['meeting-comment-delete-' + comment.id]">
                    <v-icon left>mdi-delete-outline</v-icon>
                    削除
                  </v-btn>
                </v-col>
              </v-row>
            </div>
          </v-card-text>

        </v-card-text>
      </v-card>

      <!-- メモ投稿ダイアログ -->
      <v-dialog v-model="formMeetingCommentDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
        @click:outside="resetMeetingCommentDialog">
        <v-card flat tile>
          <v-card-title>メモを書く</v-card-title>
          <v-card-text>
            <v-list>
              <v-list-item>
                <v-textarea dense filled prepend-inner-icon="mdi-pencil" v-model="formMeetingComment.comment"
                  :error="Boolean(formMeetingComment.errors.comment)"
                  :error-messages="formMeetingComment.errors.comment" />
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <v-btn color="primary" @click.native="meetingCommentCreate" :loading="loading['meeting-comment-create']">
              <v-icon left>mdi-content-save-outline</v-icon> この内容で投稿する
            </v-btn>
          </v-card-text>

          <v-divider />

          <v-card-text class="text-right">
            <v-btn class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="resetMeetingCommentDialog">
              <v-icon>mdi-close</v-icon> 閉じる
            </v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  name: "MeetingsShow",
  components: { Layout, Link },
  props: ["meeting", "users", "user"],
  data() {
    return {
      formMeetingCommentDialog: false,
      formMeetingComment: this.$inertia.form({
        meeting_id: this.meeting.id,
        comment: null,
      }),
      loading: {},
    };
  },
  computed: {
    isOwner() {
      return Number(this.meeting.user_id) === Number(this.$page.props.auth.user.id);
    },
  },
  methods: {
    isCommentOwner(userId) {
      return Number(userId) === Number(this.$page.props.auth.user.id);
    },
    meetingCommentCreate() {
      this.formMeetingComment.post(this.$route("meeting-comments.store", { meeting: this.meeting.id }), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "meeting-comment-create", true),
        onSuccess: () => {
          this.$toasted.show("メモを追加しました");
          this.resetMeetingCommentDialog();
        },
        onFinish: () => this.$set(this.loading, "meeting-comment-create", false),
      });
    },
    resetMeetingCommentDialog() {
      this.formMeetingCommentDialog = false;
      this.formMeetingComment.clearErrors();
      this.formMeetingComment.reset();
    },
    meetingCommentDelete(id) {
      this.$confirm("このメモを削除してよろしいですか？<br>削除した項目を元に戻すことはできません").then((ok) => {
        if (ok) {
          this.formMeetingComment.delete(this.$route("meeting-comments.destroy", { meeting_comment: id }), {
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "meeting-comment-delete-" + id, true),
            onSuccess: () => this.$toasted.show("メモを削除しました"),
            onFinish: () => this.$set(this.loading, "meeting-comment-delete-" + id, false),
          });
        }
      });
    },
    meetingLikeChange(id) {
      this.$inertia.put(this.$route("meetings.like", { meeting: id }), {}, { preserveScroll: true });
    },
  },
};
</script>

<style scoped>
.visited-user {
  background: linear-gradient(transparent 60%, #00ccff 60%);
}

.v-card,
.v-dialog,
.v-btn,
.v-chip {
  border-radius: 12px !important;
}
</style>
