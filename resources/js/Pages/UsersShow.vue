<style>
.fc-day-sat {
  background-color: #eaf4ff;
}

.fc-day-sun {
  background-color: #ffeaea;
}
</style>

<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text v-if="Number(user.id) === Number($page.props.auth.user.id)">
        <v-row>
          <v-col class="text-right">
            <Button :small="$vuetify.breakpoint.xs" :to="$route('user-profile-information.edit')">
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-text>
        <div class="description-list">
          <v-row>
            <v-col cols="12" sm="4">メンバーID</v-col>
            <v-col>
              {{ user.username }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">メンバー名</v-col>
            <v-col>
              {{ user.name }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">登録日時</v-col>
            <v-col>
              {{ user.created_at }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">メールアドレス</v-col>
            <v-col>
              <a :href="'mailto:' + user['email']">{{ user["email"] }}</a>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">電話番号</v-col>
            <v-col>
              <a :href="'tel:' + user['tel']">{{ user["tel"] }}</a>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">所属部署</v-col>
            <v-col>
              {{ user.department }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">担当商材</v-col>
            <v-col>
              <div v-for="product in user['products']">
                {{ product.name }}
              </div>
            </v-col>
          </v-row>
        </div>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <v-card flat tile>


      <FullCalendar class='demo-app-calendar' :options='calendarOptions' ref="fullCalendar">

      </FullCalendar>

      <v-divider class="mx-4"></v-divider>


    </v-card>

    <!-- イベント追加ダイアログ -->
    <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '1000px' : 'unset'">
      <v-card flat tile>
        <form @submit.prevent="create">
          <input type="hidden" name="route">
          <v-card-text>
            <div class="description-form">

              <v-row v-if="!form.rangeEnabled">
                <v-col cols="12" sm="2">日付<br><v-checkbox v-model="form.rangeEnabled" label="期間設定"
                    pt="0"></v-checkbox></v-col>
                <v-col>
                  <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="form.date"
                    :error="Boolean(form.errors.date)" :error-messages="form.errors.date"></v-text-field>
                </v-col>
              </v-row>

              <v-row v-if="form.rangeEnabled">
                <v-col cols="12" sm="2">日付<br><v-checkbox v-model="form.rangeEnabled" label="期間設定"
                    pt="0"></v-checkbox></v-col>
                <v-col>
                  <v-text-field prepend-icon="mdi-calendar" type="date" name="start_date" v-model="form.start_date"
                    label="開始日"></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field prepend-icon="mdi-calendar" type="date" name="end_date" v-model="form.end_date"
                    label="終了日"></v-text-field>
                </v-col>
              </v-row>

              <v-row v-if="form.rangeEnabled">
                <v-col cols="12" sm="2">曜日設定</v-col>
                <v-col>
                  <v-checkbox label="月曜日" pt="0" v-model="form.monday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="火曜日" pt="0" v-model="form.tuesday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="水曜日" pt="0" v-model="form.wednesday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="木曜日" pt="0" v-model="form.thursday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="金曜日" pt="0" v-model="form.friday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="土曜日" pt="0" v-model="form.saturday"></v-checkbox>
                </v-col>
                <v-col>
                  <v-checkbox label="日曜日" pt="0" v-model="form.sunday"></v-checkbox>
                </v-col>

              </v-row>


              <v-row>
                <v-col cols="12" sm="2">時間<br><v-checkbox v-model="form.enabled" label="終日" pt="0"></v-checkbox></v-col>

                <v-col>
                  <v-text-field type="time" name="start_time" v-model="form.start_time" :disabled="form.enabled"
                    label="開始時間" :error="Boolean(form.errors.start_time)"
                    :error-messages="form.errors.start_time"></v-text-field>
                </v-col>

                <v-col>
                  <v-text-field type="time" name="end_time" v-model="form.end_time" :disabled="form.enabled" label="終了時間"
                    :error="Boolean(form.errors.end_time)" :error-messages="form.errors.end_time"></v-text-field>
                </v-col>
              </v-row>


              <v-row>
                <v-col cols="12" sm="2">タイトル</v-col>
                <v-col>
                  <v-select label="スケージュール種別" name="title_type" v-model="form.title_type"
                    :items="['営業/外出', '出張', '社内業務', '会議', '来客対応', 'テレワーク', 'その他']" variant="underlined"></v-select>
                </v-col>
                <v-col>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="title" v-model="form.title"
                    maxlength="200" :error="Boolean(form.errors.title)"
                    :error-messages="form.errors.title"></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="2">内容</v-col>
                <v-col>
                  <v-textarea dense filled prepend-inner-icon="mdi-pencil" name="content" v-model="form.content"
                    maxlength="200" counter="200" :error="Boolean(form.errors.content)"
                    :error-messages="form.errors.content"></v-textarea>
                </v-col>
              </v-row>



            </div>

            <v-row>
              <v-col class="text-right">
                <Button :small="$vuetify.breakpoint.xs" color="primary" type="submit" :loading="loading['create']">
                  <v-icon left>
                    mdi-content-save-outline
                  </v-icon>
                  この内容で作成する
                </Button>
              </v-col>
            </v-row>
          </v-card-text>
        </form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="displaySchedule" :max-width="$vuetify.breakpoint.smAndUp ? '1000px' : 'unset'">


      <v-card flat tile>
        <v-card-text mt="10">
          <form @submit.prevent="update">


            <div class="description-form">

              <v-row>
                <v-col cols="12" sm="2">登録者</v-col>
                <v-col>
                  <v-text-field type="name" name="user_name" v-model="edit_form.user_name"
                    :disabled="true"></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="2">日付</v-col>
                <v-col>
                  <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="edit_form.date"
                    :error="Boolean(edit_form.errors.date)" :error-messages="edit_form.errors.date"></v-text-field>
                </v-col>
              </v-row>


              <v-row>
                <v-col cols="12" sm="2">時間<br><v-checkbox name="enabled" v-model="edit_form.enabled" label="終日"
                    pt="0"></v-checkbox></v-col>

                <v-col>
                  <v-text-field type="time" name="start_time" v-model="edit_form.start_time" :disabled="edit_form.enabled"
                    label="開始時間" :error="Boolean(edit_form.errors.start_time)"
                    :error-messages="edit_form.errors.start_time"></v-text-field>
                </v-col>

                <v-col>
                  <v-text-field type="time" name="end_time" v-model="edit_form.end_time" :disabled="edit_form.enabled"
                    label="終了時間" :error="Boolean(edit_form.errors.end_time)"
                    :error-messages="edit_form.errors.end_time"></v-text-field>
                </v-col>
              </v-row>


              <v-row>
                <v-col cols="12" sm="2">タイトル</v-col>
                <v-col>
                  <v-select label="スケージュール種別" name="title_type" v-model="edit_form.title_type"
                    :items="['営業/外出', '出張', '社内業務', '会議', '来客対応', 'テレワーク', 'その他']" variant="underlined"></v-select>
                </v-col>
                <v-col>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="title" v-model="edit_form.title"
                    maxlength="200" :error="Boolean(edit_form.errors.title)"
                    :error-messages="edit_form.errors.title"></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="2">内容</v-col>
                <v-col>
                  <v-textarea dense filled prepend-inner-icon="mdi-pencil" name="content" v-model="edit_form.content"
                    maxlength="200" counter="200" :error="Boolean(edit_form.errors.content)"
                    :error-messages="edit_form.errors.content"></v-textarea>
                </v-col>
              </v-row>

            </div>


            <v-row v-if="this.loginUser.id == this.loginUser.id">

              <form @submit.prevent="deleteData">
                <input type="hidden" name="id" v-model="this.delete_form.id" />
                <v-col class="text-left">
                  <Button :small="$vuetify.breakpoint.xs" color="danger" type="submit" :loading="loading['delete']">
                    <v-icon left>
                      mdi-content-save-outline
                    </v-icon>
                    削除する
                  </Button>
                </v-col>
              </form>

              <v-col class="text-right">
                <Button :small="$vuetify.breakpoint.xs" color="primary" type="submit" :loading="loading['update']">
                  <v-icon left>
                    mdi-content-save-outline
                  </v-icon>
                  内容を更新する
                </Button>
              </v-col>
            </v-row>
          </form>


        </v-card-text>
      </v-card>



    </v-dialog>



  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import jaLocale from '@fullcalendar/core/locales/ja'
import axios from "axios";
import { INITIAL_EVENTS, createEventId } from './event-utils'
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling


export default {
  components: { Layout, Link, FullCalendar },

  props: ['schedule', 'user', 'loginUser'],

  data() {
    return {
      showDialog: false,
      displaySchedule: false,

      form: this.$inertia.form({
        user_id: Number(this.user.id) ?? 0,
        date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        start_time: null,
        end_time: null,
        title: null,
        title_type: null,
        content: null,
        start_date: null,
        end_date: null,
        rangeEnabled: false,
        enabled: true,
        monday: true,
        tuesday: true,
        wednesday: true,
        thursday: true,
        friday: true,
        saturday: false,
        sunday: false,
      }),

      edit_form: this.$inertia.form({
        id: 0,
        user_id: 0,
        date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        start_time: null,
        end_time: null,
        title: null,
        title_type: null,
        content: null,
        user_name: null,
        enabled: false,
      }),
      delete_form: this.$inertia.form({
        id: 0,
      }),


      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locales: [jaLocale],
        locale: "ja",
        businessHours: "true",
        height: "auto",
        firstDay: "1",
        weekends: "true",
        dateClick: this.handleDateClick,
        events: this.schedule,
        eventClick: this.handleEventClick,
        headerToolbar: {
          // title, prev, next, prevYear, nextYear, today
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth timeGridWeek'
        },
        eventDidMount: this.handleEventDidMount,
        eventMouseEnter: this.handleEventMouseEnter,
      },
      loading: {},
    }
  },

  methods: {

    handleDateClick: function (info) {
      console.log(this.loginUser.id);
      console.log(this.user.id);
      if (this.loginUser.id == this.user.id) {
        console.log(info.dateStr);
        this.showDialog = true;
        this.dateString = info.dateStr;
        this.form.date = info.dateStr;
        this.form.start_date = info.dateStr;
        this.form.end_date = info.dateStr;
      }

    },
    handleEventClick: function (eventObj, el) {

      console.log(eventObj.event.classNames);
      if (eventObj.event.classNames == 'sales_part_todo_list' || eventObj.event.classNames == 'office_part_todo_list') {

        return;
      }

      axios.get(`/schedule/getData/${eventObj.event.id}`)
        .then((res) => {
          this.displaySchedule = true;
          this.edit_form.id = res.data.id;
          this.edit_form.title = res.data.title;
          this.edit_form.date = res.data.date;
          this.edit_form.content = res.data.content;
          this.edit_form.user_name = res.data.userName;
          this.edit_form.start_time = res.data.start_time;
          this.edit_form.end_time = res.data.end_time;
          this.edit_form.enabled = res.data.start_time ? false : true;
          this.edit_form.title_type = res.data.title_type ?? null;
          this.delete_form.id = res.data.id;

        })
        .catch((e) => {

        })
        .finally(() => {

        });

    },
    handleEventLeave: function (eventObj, el) {
      this.displaySchedule = false;
    },
    

    handleEventMouseEnter(e) {
            console.log(e.event),
            tippy(e.el, {
                content: `<div style="">
                  <p>${e.event.extendedProps.pops_time}<p><br/>
                 <p>${e.event.extendedProps.pops_tile}<p><br/>
                 <p>${e.event.extendedProps.content}<p><br/>
                </div>`,
                allowHTML: true,
                theme: 'light-border'
            });
        },


    create: function () {

      this.form.post(this.$route('schedule.store'), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('スケジュールの作成を完了しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      }
      )
    },
    update: function () {
      this.edit_form.put(this.$route('schedule.update', this.edit_form.id), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "update", true),
        onSuccess: () => this.$toasted.show('スケジュールの作成を更新しました'),
        onFinish: () => this.$set(this.loading, "update", false),
      }
      )
    },
    deleteData: function () {
      this.delete_form.post(this.$route('schedule.delete'), {
        preserveState: (page) => Object.keys(page.props.errors).length,
        onStart: () => this.$set(this.loading, "delete", true),
        onSuccess: () => this.$toasted.show('スケジュールの作成を更新しました'),
        onFinish: () => this.$set(this.loading, "delete", false),
      }
      )
    },
  },
  created() {
    console.log(this.schedule)
  }

}
</script>