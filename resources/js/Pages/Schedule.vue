<style scoped>
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


            <FullCalendar class='demo-app-calendar' :options='calendarOptions' ref="fullCalendar">

            </FullCalendar>

            <v-divider class="mx-4"></v-divider>


        </v-card>

        <!-- イベント追加ダイアログ -->
        <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '1000px' : 'unset'">
            <v-card flat tile>
                <form @submit.prevent="create">
                    <v-card-text>
                        <div class="description-form">

                            <v-row v-if="!form.rangeEnabled">
                                <v-col cols="12" sm="2">日付<br><v-checkbox v-model="form.rangeEnabled" label="期間設定"
                                        pt="0"></v-checkbox></v-col>
                                <v-col>
                                    <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="form.date"
                                        :error="Boolean(form.errors.date)"
                                        :error-messages="form.errors.date"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row v-if="form.rangeEnabled">
                                <v-col cols="12" sm="2">日付<br><v-checkbox v-model="form.rangeEnabled" label="期間設定"
                                        pt="0"></v-checkbox></v-col>
                                <v-col>
                                    <v-text-field prepend-icon="mdi-calendar" type="date" name="start_date"
                                        v-model="form.start_date" label="開始日"></v-text-field>
                                </v-col>
                                <v-col>
                                    <v-text-field prepend-icon="mdi-calendar" type="date" name="end_date"
                                        v-model="form.end_date" label="終了日"></v-text-field>
                                </v-col>
                            </v-row>


                            <v-row>
                                <v-col cols="12" sm="2">時間<br><v-checkbox v-model="form.enabled" label="終日"
                                        pt="0"></v-checkbox></v-col>

                                <v-col>
                                    <v-text-field type="time" name="start_time" v-model="form.start_time"
                                        :disabled="form.enabled" label="開始時間" :error="Boolean(form.errors.start_time)"
                                        :error-messages="form.errors.start_time"></v-text-field>
                                </v-col>

                                <v-col>
                                    <v-text-field type="time" name="end_time" v-model="form.end_time" :disabled="form.enabled"
                                        label="終了時間" :error="Boolean(form.errors.end_time)"
                                        :error-messages="form.errors.end_time"></v-text-field>
                                </v-col>
                            </v-row>


                            <v-row>
                                <v-col cols="12" sm="2">タイトル</v-col>
                                <v-col>
                                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="title"
                                        v-model="form.title" maxlength="200" :error="Boolean(form.errors.title)"
                                        :error-messages="form.errors.title"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" sm="2">内容</v-col>
                                <v-col>
                                    <v-textarea dense filled prepend-inner-icon="mdi-pencil" name="content"
                                        v-model="form.content" maxlength="200" counter="200"
                                        :error="Boolean(form.errors.content)"
                                        :error-messages="form.errors.content"></v-textarea>
                                </v-col>
                            </v-row>



                        </div>

                        <v-row>
                            <v-col class="text-right">
                                <Button :small="$vuetify.breakpoint.xs" color="primary" type="submit"
                                    :loading="loading['create']">
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
            <form @submit.prevent="update">

                <v-card flat tile>
                    <v-card-text mt="10">


                        <div class="description-form">

                            <v-row>
                                <v-col cols="12" sm="2">登録者</v-col>
                                <v-col>
                                    <v-text-field type="name" name="user_name" v-model="edit_form.user_name" :disabled="true"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" sm="2">日付</v-col>
                                <v-col>
                                    <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="edit_form.date"
                                        :error="Boolean(edit_form.errors.date)"
                                        :error-messages="edit_form.errors.date"></v-text-field>
                                </v-col>
                            </v-row>


                            <v-row>
                                <v-col cols="12" sm="2">時間<br><v-checkbox name="enabled" v-model="edit_form.enabled" label="終日"
                                        pt="0"></v-checkbox></v-col>

                                <v-col>
                                    <v-text-field type="time" name="start_time" v-model="edit_form.start_time"
                                        :disabled="edit_form.enabled" label="開始時間"></v-text-field>
                                </v-col>

                                <v-col>
                                    <v-text-field type="time" name="end_time" v-model="edit_form.end_time"
                                        :disabled="edit_form.enabled" label="終了時間"></v-text-field>
                                </v-col>
                            </v-row>


                            <v-row>
                                <v-col cols="12" sm="2">タイトル</v-col>
                                <v-col>
                                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="title"
                                        v-model="edit_form.title" maxlength="200" :error="Boolean(edit_form.errors.title)"
                                        :error-messages="edit_form.errors.title"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" sm="2">内容</v-col>
                                <v-col>
                                    <v-textarea dense filled prepend-inner-icon="mdi-pencil" name="content"
                                        v-model="edit_form.content" maxlength="200" counter="200"
                                        :error="Boolean(edit_form.errors.content)"
                                        :error-messages="edit_form.errors.content"></v-textarea>
                                </v-col>
                            </v-row>

                        </div>


                        <v-row>
                            <v-col class="text-right">
                                <Button :small="$vuetify.breakpoint.xs" color="primary" type="submit"
                                    :loading="loading['update']">
                                    <v-icon left>
                                        mdi-content-save-outline
                                    </v-icon>
                                    内容を更新する
                                </Button>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </form>
        </v-dialog>

    </Layout>
</template>
  
<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import jaLocale from '@fullcalendar/core/locales/ja'
import axios from "axios";

// console.log(this.schedule);

const dateString = '';

export default {
    components: { Layout, Link, FullCalendar },

    props: ['schedule', 'user'],

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
                content: null,
                start_date:null,
                end_date:null,
                rangeEnabled:false,
                enabled: true,
            }),

            edit_form: this.$inertia.form({
                id: 0,
                user_id: 0,
                date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                start_time: null,
                end_time: null,
                title: null,
                content: null,
                user_name: null,
                enabled: false,
            }),

          
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
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
            },
            loading: {},
        }
    },

    methods: {
        handleDateClick: function (info) {
            console.log(info.dateStr);
            this.showDialog = true;
            this.dateString = info.dateStr;
            this.form.date = info.dateStr;
            this.form.start_date = info.dateStr;
            this.form.end_date = info.dateStr;
        },
        handleEventClick: function (eventObj, el) {
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

                })
                .catch((e) => {

                })
                .finally(() => {

                });

        },
        handleEventLeave: function (eventObj, el) {
            this.displaySchedule = false;
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
            this.edit_form.put(this.$route('schedule.update', this.edit_form.id ), {
                preserveState: (page) => Object.keys(page.props.errors).length,
                onStart: () => this.$set(this.loading, "update", true),
                onSuccess: () => this.$toasted.show('スケジュールの作成を更新しました'),
                onFinish: () => this.$set(this.loading, "update", false),
            }
            )
        },
    },
    created() {
        console.log(this.schedule)
    }

}
</script>
  