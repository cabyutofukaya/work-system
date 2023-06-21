<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-text class="pb-0 text-right text-body-2">
        お疲れ様です、{{ $page.props.auth.user.name }}さん!
      </v-card-text>
    </v-card>

    <!-- 新着のお知らせ -->
    <v-card
        flat tile
        class="mt-6"
        dusk="notices"
    >
      <v-card-title>
        <v-icon dark left>mdi-information</v-icon>
        新着のお知らせ
      </v-card-title>

      <v-list>
        <div v-for="notice in notices" :key="notice.id">
          <Link
              :href="$route('notices.show', {id: notice.id})" exact
              as="v-list-item"
              dusk="noticeShow"
          >
            <v-list-item-content>
              <v-list-item-title class="mb-1">{{ notice["created_at"] }}</v-list-item-title>

              <v-list-item-subtitle>
                <div class="mb-1">{{ notice["title"] }}</div>
                <div>{{ notice["user"]["name"] }}</div>
              </v-list-item-subtitle>
            </v-list-item-content>
          </Link>

          <v-divider></v-divider>
        </div>
      </v-list>

      <v-card-text class="text-right" v-if="notices.length">
        <Button
            class="mb-2 mr-2"
            :small="$vuetify.breakpoint.xs"
            :to="$route('notices.index')"
            dusk="noticeIndex"
        >
          <v-icon>
            mdi-format-list-bulleted
          </v-icon>
          すべてのお知らせを見る
        </Button>
      </v-card-text>

      <v-card-text v-if="!notices.length">
        お知らせはありません
      </v-card-text>
    </v-card>


     <!-- 新着の議事録 -->
     <v-card
        flat tile
        class="mt-6"
        dusk="meeting"
        v-if="meetings.length"
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document-edit</v-icon>
        新着の議事録
      </v-card-title>

    
      <v-list v-if="meetings.length">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="2">
                  開催日時
                </v-col>
                <v-col cols="12" sm="6">
                  会議名
                </v-col>
                <v-col cols="12" sm="2">
                  名前
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  いいね
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  コメント
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="meeting in meetings" :key="meeting.id">
          <Link as="v-list-item" :href="$route('meetings.show', {id: meeting.id})">
            <v-list-item-content>
              <v-row>
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2">
                    {{ meeting.started_at }}
                  </v-col>
                  <v-col sm="6">
                    {{ meeting.title }}
                  </v-col>
                  <v-col sm="2">
                    {{ meeting.user.name }}            
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1">{{ meeting.started_at }}</div>
                    <div class="mb-1">{{ meeting.title }}</div>
                    <div class="mb-6">{{ meeting.user.name }}</div>
                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ meeting["meeting_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center">
                    {{ meeting["meeting_comments_count"] }}
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ meeting["meeting_likes_count"] }}
                    </v-chip>
                    <v-chip small class="mr-2">
                      コメント
                      {{ meeting["meeting_comments_count"] }}
                    </v-chip>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </Link>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <div class="mt-4 text-right" v-if="meetings.length">
        <Button
            class="mb-2 mr-2"
            :small="$vuetify.breakpoint.xs"
            :to="$route('meetings.index')"
            dusk="salesTodoIndex"
        >
          <v-icon>
            mdi-format-list-bulleted
          </v-icon>
          すべての議事録を見る
        </Button>
      </div>

      
    </v-card>
    

    <!-- 直近の営業ToDoリスト  -->
    <v-card
        flat tile
        class="mt-6"
        dusk="sales_todos"
    >
      <v-card-title>
        <v-icon dark left>mdi-calendar</v-icon>
        直近の営業ToDoリスト
      </v-card-title>

      <v-list class="dense-list py-0" v-if="sales_todos.length">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="3">
                  日時 / 会社 / 相手先担当者
                </v-col>
                <v-col cols="12" sm="5">
                  要件
                </v-col>
                <v-col cols="12" sm="1" style="white-space:nowrap">
                  社内担当者
                </v-col>
                <v-col cols="12" sm="3">

                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="sales_todo in sales_todos" :key="sales_todo.id">
          <v-list-item>
            <v-list-item-content>
              <v-row :class="{'grey': sales_todo['is_completed'], 'lighten-4': sales_todo['is_completed']}">
                <v-col cols="12" sm="3" class="grey lighten-3 px-6">
                  <div>
                    {{ sales_todo['scheduled_at'] }}
                  </div>

                  <div class="d-flex align-center">
                    <div>
                      <Link :href="$route('clients.show', { client: sales_todo['client_id']})" dusk="clientShowSalesTodo">
                        <v-list-item-avatar tile>
                          <v-img
                              :src="sales_todo['client']['icon_image_url']" alt=""
                          ></v-img>
                        </v-list-item-avatar>
                      </Link>
                    </div>

                    <div>
                      <div>
                        <Link :href="$route('clients.show', { client: sales_todo['client_id']})" dusk="clientShowSalesTodo">
                          {{ sales_todo['client']['name'] }}
                        </Link>
                      </div>
                      <div class="mt-1">
                        <span v-if="$vuetify.breakpoint.xs">相手先担当者：</span>{{ sales_todo['contact_person'] }}
                      </div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" sm="5">
                  <span style="white-space: pre-line;">{{ sales_todo["description"] }}</span>
                </v-col>

                <!-- 社内担当者 PCビュー -->
                <template v-if="!$vuetify.breakpoint.xs">
                  <v-col cols="1">
                    <div
                        v-for="(sales_todo_participant, index) in sales_todo['sales_todo_participants']" :key="index"
                        class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                        style="white-space:nowrap"
                    >
                      <Link :href="$route('users.show', {user: sales_todo_participant['user_id']})" dusk="userShowSalesTodoParticipant">
                        {{ sales_todo_participant['user']['name'] }}
                      </Link>
                    </div>
                  </v-col>
                </template>

                <!-- 社内担当者 スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" v-if="sales_todo['sales_todo_participants'].length">
                    社内担当者：<span
                      v-for="(sales_todo_participant, index) in sales_todo['sales_todo_participants']" :key="index"
                      class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                  >
                        <Link :href="$route('users.show', {user: sales_todo_participant['user_id']})" dusk="userShowSalesTodoParticipant">
                          {{ sales_todo_participant['user']['name'] }}
                        </Link>
                      </span>
                  </v-col>
                </template>

                <v-col cols="12" sm="3" class="text-right">
                  <Button
                      v-if="!sales_todo['is_completed']"
                      color="primary" dark
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="closeSalesTodo(sales_todo.id)"
                      :loading="loading['sales-todo-complete-' + sales_todo.id]"
                      dusk="salesTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      v-if="sales_todo['is_completed']"
                      color="primary" outlined
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="openSalesTodo(sales_todo.id)"
                      :loading="loading['sales-todo-complete-' + sales_todo.id]"
                      dusk="salesTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      :color="sales_todo['is_completed'] ? 'grey' : undefined"
                      :outlined="sales_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      :to="$route('sales-todos.edit', {sales_todo: sales_todo.id})"
                      dusk="salesTodoEdit"
                  >
                    <v-icon left>
                      mdi-pencil
                    </v-icon>
                    編集
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      color="error"
                      :outlined="sales_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="deleteSalesTodo(sales_todo.id)"
                      :loading="loading['delete-sales-todo-' + sales_todo.id]"
                      dusk="salesTodoDelete"
                  >
                    <v-icon left>
                      mdi-delete-outline
                    </v-icon>
                    削除
                  </Button>
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <div class="mt-4 text-right" v-if="sales_todos.length">
        <Button
            class="mb-2 mr-2"
            :small="$vuetify.breakpoint.xs"
            :to="$route('sales-todos.index')"
            dusk="salesTodoIndex"
        >
          <v-icon>
            mdi-format-list-bulleted
          </v-icon>
          すべての営業ToDoを見る
        </Button>
      </div>

      <v-card-text v-if="!sales_todos.length">
        営業ToDoはありません
      </v-card-text>
    </v-card>

    <!-- 直近の社内ToDoリスト  -->
    <v-card
        flat tile
        class="mt-6"
        dusk="office_todos"
    >
      <v-card-title>
        <v-icon dark left>mdi-calendar</v-icon>
        直近の社内ToDoリスト
      </v-card-title>

      <v-list class="dense-list py-0" v-if="office_todos.length">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="3">
                  日時 / タイトル
                </v-col>
                <v-col cols="12" sm="5">
                  メモ
                </v-col>
                <v-col cols="12" sm="1" style="white-space:nowrap">
                  社内担当者
                </v-col>
                <v-col cols="12" sm="3">

                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="office_todo in office_todos" :key="office_todo.id">
          <v-list-item>
            <v-list-item-content>
              <v-row :class="{'grey': office_todo['is_completed'], 'lighten-4': office_todo['is_completed']}">
                <v-col cols="12" sm="3" class="grey lighten-3 px-6">
                  <div>
                    {{ office_todo['scheduled_at'] }}
                  </div>

                  <div class="mt-1">
                    {{ office_todo['title'] }}
                  </div>
                </v-col>

                <v-col cols="12" sm="5">
                  <span style="white-space: pre-line;">{{ office_todo["description"] }}</span>
                </v-col>

                <!-- 社内担当者 PCビュー -->
                <template v-if="!$vuetify.breakpoint.xs">
                  <v-col cols="1">
                    <div
                        v-for="(office_todo_participant, index) in office_todo['office_todo_participants']" :key="index"
                        class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                        style="white-space:nowrap"
                    >
                      <Link :href="$route('users.show', {user: office_todo_participant['user_id']})" dusk="userShowOfficeTodoParticipant">
                        {{ office_todo_participant['user']['name'] }}
                      </Link>
                    </div>
                  </v-col>
                </template>

                <!-- 社内担当者 スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" v-if="office_todo['office_todo_participants'].length">
                    社内担当者：
                    <div
                        v-for="(office_todo_participant, index) in office_todo['office_todo_participants']" :key="index"
                        class="d-inline-block mr-2" :class="{'mt-1': index !== 0}"
                    >
                      <Link :href="$route('users.show', {user: office_todo_participant['user_id']})" dusk="userShowOfficeTodoParticipant">
                        {{ office_todo_participant['user']['name'] }}
                      </Link>
                    </div>
                  </v-col>
                </template>

                <v-col cols="12" sm="3" class="text-right">
                  <Button
                      v-if="!office_todo['is_completed']"
                      color="primary" dark
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="closeOfficeTodo(office_todo.id)"
                      :loading="loading['office-todo-complete-' + office_todo.id]"
                      dusk="officeTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      v-if="office_todo['is_completed']"
                      color="primary" outlined
                      class="ml-2 mb-1"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="openOfficeTodo(office_todo.id)"
                      :loading="loading['office-todo-complete-' + office_todo.id]"
                      dusk="officeTodoComplete"
                  >
                    <v-icon left>
                      mdi-checkbox-marked-outline
                    </v-icon>
                    完了
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      :color="office_todo['is_completed'] ? 'grey' : undefined"
                      :outlined="office_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      :to="$route('office-todos.edit', {office_todo: office_todo.id})"
                      dusk="officeTodoEdit"
                  >
                    <v-icon left>
                      mdi-pencil
                    </v-icon>
                    編集
                  </Button>

                  <Button
                      class="ml-2 mb-1"
                      color="error"
                      :outlined="office_todo['is_completed']"
                      :small="$vuetify.breakpoint.xs"
                      @click.native.prevent="deleteOfficeTodo(office_todo.id)"
                      :loading="loading['delete-office-todo-' + office_todo.id]"
                      dusk="officeTodoDelete"
                  >
                    <v-icon left>
                      mdi-delete-outline
                    </v-icon>
                    削除
                  </Button>
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <div class="mt-4 text-right" v-if="office_todos.length">
        <Button
            class="mb-2 mr-2"
            :small="$vuetify.breakpoint.xs"
            :to="$route('office-todos.index')"
            dusk="officeTodoIndex"
        >
          <v-icon>
            mdi-format-list-bulleted
          </v-icon>
          すべての社内ToDoを見る
        </Button>
      </div>

      <v-card-text v-if="!office_todos.length">
        社内ToDoはありません
      </v-card-text>
    </v-card>

    <!-- 最新の日報 -->
    <v-card
        flat tile
        class="mt-6"
        dusk="reports"
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document</v-icon>
        最新の日報
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col v-if="!reports.length">
            まだ日報はありません
          </v-col>

          <v-col class="text-right">
            <Button
                :small="$vuetify.breakpoint.xs"
                :to="$route('reports.create')"
            >
              <v-icon left>
                mdi-plus
              </v-icon>
              日報を作成する
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-list v-if="reports.length">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="2">
                  日付
                </v-col>
                <v-col cols="12" sm="2">
                  名前
                </v-col>
                <v-col cols="12" sm="6">
                  日報の種類
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  いいね
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  コメント
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="report in reports" :key="report.id">
          <Link as="v-list-item" :href="$route('reports.show', {id: report.id})">
            <v-list-item-content>
              <v-row>
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2">
                    {{ report.date }}
                  </v-col>
                  <v-col sm="2">
                    {{ report.user.name }}
                  </v-col>
                  <v-col sm="6">
                    <span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1">{{ report.date }}</div>
                    <div class="mb-1">{{ report.user.name }}</div>
                    <div><span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                    </div>
                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ report["report_content_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center">
                    {{ report["report_comments_count"] }}
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ report["report_content_likes_count"] }}
                    </v-chip>
                    <v-chip small class="mr-2">
                      コメント
                      {{ report["report_comments_count"] }}
                    </v-chip>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </Link>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <div class="mt-4 text-right" v-if="reports_latest_comment.length">
        <Button
            class="mb-2 mr-2"
            :small="$vuetify.breakpoint.xs"
            :to="$route('reports.index')"
        >
          <v-icon>
            mdi-format-list-bulleted
          </v-icon>
          すべての日報を見る
        </Button>
      </div>
    </v-card>

    <!-- コメントがついた日報 -->
    <v-card
        flat tile
        class="mt-6"
        dusk="reports_latest_comment"
    >
      <v-card-title>
        <v-icon dark left>mdi-file-document</v-icon>
        コメントがついた日報
      </v-card-title>

      <v-list v-if="reports_latest_comment.length">
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row>
                <v-col cols="12" sm="2">
                  日付
                </v-col>
                <v-col cols="12" sm="2">
                  名前
                </v-col>
                <v-col cols="12" sm="6">
                  日報の種類
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  いいね
                </v-col>
                <v-col cols="12" sm="1" class="text-center">
                  コメント
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>

          <v-divider class="mx-4"></v-divider>
        </template>

        <div v-for="report in reports_latest_comment" :key="report.id">
          <Link as="v-list-item" :href="$route('reports.show', {id: report.id})">
            <v-list-item-content>
              <v-row>
                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="2">
                    {{ report.date }}
                  </v-col>
                  <v-col sm="2">
                    {{ report.user.name }}
                  </v-col>
                  <v-col sm="6">
                    <span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12">
                    <div class="mb-1">{{ report.date }}</div>
                    <div class="mb-1">{{ report.user.name }}</div>
                    <div><span v-if="report['report_contents_sales_exists']">営業日報</span><span v-if="report['report_contents_sales_exists'] && report['report_contents_work_exists']">・</span><span v-if="report['report_contents_work_exists']">業務日報</span>
                    </div>
                  </v-col>
                </template>

                <!-- PCビュー -->
                <template v-if="$vuetify.breakpoint.smAndUp">
                  <v-col sm="1" class="text-center">
                    {{ report["report_content_likes_count"] }}
                  </v-col>
                  <v-col sm="1" class="text-center">
                    {{ report["report_comments_count"] }}
                  </v-col>
                </template>

                <!-- スマートフォンビュー -->
                <template v-else>
                  <v-col cols="12" class="text-right">
                    <v-chip small class="mr-2">
                      いいね
                      {{ report["report_content_likes_count"] }}
                    </v-chip>
                    <v-chip small class="mr-2">
                      コメント
                      {{ report["report_comments_count"] }}
                    </v-chip>
                  </v-col>
                </template>
              </v-row>
            </v-list-item-content>
          </Link>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <v-card-text v-if="!reports_latest_comment.length">
        まだコメントがついた日報はありません
      </v-card-text>
    </v-card>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['notices', 'sales_todos', 'office_todos', 'reports', 'reports_latest_comment','meetings'],

  data: () => ({
    loading: {},
  }),

  methods: {
    // 営業ToDo対応済み
    closeSalesTodo: function (id) {
      this.$confirm(
          "このToDoを対応済みにしてよろしいですか？<br>対応済みに変更すると未対応のリストの後ろに移動されます",
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('sales-todos.complete', {sales_todo: id}), {}, {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "sales-todo-complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを対応済みにしました'),
            onFinish: () => this.$set(this.loading, "sales-todo-complete-" + id, false),
          })
        }
      })
    },

    // 営業ToDo対応済み解除
    openSalesTodo: function (id) {
      this.$confirm(
          'このToDoを対応済みから未対応に戻します。<br>よろしいですか？',
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('sales-todos.complete', {sales_todo: id}), {}, {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "sales-todo-complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを未対応にしました'),
            onFinish: () => this.$set(this.loading, "sales-todo-complete-" + id, false),
          })
        }
      })
    },

    // 営業ToDo削除
    deleteSalesTodo: function (id) {
      this.$confirm('このToDoを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('sales-todos.destroy', {sales_todo: id}), {
            only: ['sales_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "delete-sales-todo-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを削除しました'),
            onFinish: () => this.$set(this.loading, "delete-sales-todo-" + id, false),
          })
        }
      })
    },

    // 社内ToDo対応済み設定
    closeOfficeTodo: function (id) {
      this.$confirm(
          "このToDoを対応済みにしてよろしいですか？<br>対応済みに変更すると未対応のリストの後ろに移動されます",
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('office-todos.complete', {office_todo: id}), {}, {
            only: ['office_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "office-todo-complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを対応済みにしました'),
            onFinish: () => this.$set(this.loading, "office-todo-complete-" + id, false),
          })
        }
      })
    },

    // 社内ToDo対応済み解除
    openOfficeTodo: function (id) {
      this.$confirm(
          'このToDoを対応済みから未対応に戻します。<br>よろしいですか？',
          {color: 'info'}
      ).then(isAccept => {
        if (isAccept) {
          this.$inertia.put(this.$route('office-todos.complete', {office_todo: id}), {}, {
            only: ['office_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "office-todo-complete-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを未対応にしました'),
            onFinish: () => this.$set(this.loading, "office-todo-complete-" + id, false),
          })
        }
      })
    },

    // 社内ToDo削除
    deleteOfficeTodo: function (id) {
      this.$confirm('このToDoを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('office-todos.destroy', {office_todo: id}), {
            only: ['office_todos'],
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "delete-office-todo-" + id, true),
            onSuccess: () => this.$toasted.show('ToDoを削除しました'),
            onFinish: () => this.$set(this.loading, "delete-office-todo-" + id, false),
          })
        }
      })
    },
  }
}
</script>
