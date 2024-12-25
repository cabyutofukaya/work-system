<style>
.fc-day-sat {
    background-color: #eaf4ff;
}

.fc-day-sun {
    background-color: #ffeaea;
}

.calandar_event {
    background-color: white;
}

.box {
    display: flex;
}

.box div {
    width: 90%;
    margin: 5px;
}
</style>

<template>
    <Layout>

        <!-- <v-card v-if="$vuetify.breakpoint.smAndUp" style="width: 95%;">
               
            </v-card> -->


        <!-- <v-row class="my-3">
            <v-col cols="10">
                <v-select dense filled v-model="this.form_user_id" :items="userList" item-value="id"
                    item-text="name" @change.native="changeUser"></v-select>
            </v-col>
        </v-row> -->


        <v-card flat tile>


            <!-- <v-col class="text-right">
            <a :href="$route('schedule.redirect', {department: user.department})">
              <Button :small="$vuetify.breakpoint.xs">
              <v-icon left>
                mdi-pencil
              </v-icon>
              メンバーのスケジュールを表示
            </Button>
            </a>
            
          </v-col> -->


            <template class="my-5">
                <FullCalendar :options='calendarOptions' ref="fullCalendar" style="max-width: 1000px;"
                    v-if="$vuetify.breakpoint.smAndUp">
                </FullCalendar>
            </template>

            <v-card class="my-20" v-if="!$vuetify.breakpoint.smAndUp" flat tile>

                <div style="margin: 100px;"></div>

                <template class="pa-10">
                    <div
                        style="min-width: 600px;overflow-x: scroll;-webkit-overflow-scrolling: touch;padding-bottom: 10px;display: flex;">
                        <div>
                            <FullCalendar :options='calendarOptionsSmartphone' ref="fullCalendar"
                                style="min-width: 600px;vertical-align: top;max-width: 100%;" class="my-20">
                            </FullCalendar>
                        </div>

                        <!-- ページトップスクロールボタン -->
                        <transition name="scroll-top-btn">
                            <v-btn fab dark fixed bottom right class="mb-10" color="info" style="margin-bottom: 100px;"
                                @click="this.handleDateClickSmartphone">
                                <v-icon large>mdi-plus</v-icon>
                            </v-btn>
                        </transition>

                    </div>
                </template>
            </v-card>




            <v-card-text class="text-left">

                <v-row>
                    <v-col>
                        <Button :small="$vuetify.breakpoint.xs" :to="$route('users.schedule.index')">
                            <v-icon left>
                                mdi-pencil
                            </v-icon>
                            メンバースケジュールを表示
                        </Button>
                    </v-col>
                </v-row>

            </v-card-text>





        </v-card>



        <!-- イベント追加ダイアログ(スマホ専用) -->
        <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '800px' : 'unset'"
            v-if="!$vuetify.breakpoint.smAndUp">
            <v-card flat tile>

                <form @submit.prevent="create">
                    <v-card-text>

                        <!-- 日付 -->
                        <v-list v-if="!form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="date" label="日付"
                                    v-model="form.date" :error="Boolean(form.errors.date)"
                                    :error-messages="form.errors.date"></v-text-field>
                            </v-list-item>
                        </v-list>


                        <!-- 期間設定 -->
                        <v-list v-if="form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="start_date" label="開始日"
                                    v-model="form.start_date" :error="Boolean(form.errors.start_date)"
                                    :error-messages="form.errors.start_date"></v-text-field>

                                <v-text-field type="date" name="end_date" label="終了日" v-model="form.end_date"
                                    :error="Boolean(form.errors.end_date)" :error-messages="form.errors.end_date"
                                    class="ml-10"></v-text-field>
                            </v-list-item>
                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="warning" label="期間" name="rangeEnabled"
                                    v-model="form.rangeEnabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list v-if="form.rangeEnabled" style="margin-top: -20px;" class="mb-10">
                            <v-list-item>
                                <v-switch dense filled class="mr-4" color="primary" hint="月曜日" persistent-hint
                                    v-model="form.monday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="火曜日" persistent-hint
                                    v-model="form.tuesday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="水曜日" persistent-hint
                                    v-model="form.wednesday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="木曜日" persistent-hint
                                    v-model="form.thursday"></v-switch>

                            </v-list-item>


                            <v-list-item>

                                <v-switch dense filled class="mr-4" color="primary" hint="金曜日" persistent-hint
                                    v-model="form.friday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="土曜日" persistent-hint
                                    v-model="form.saturday"></v-switch>
                                <v-switch dense filled color="primary" hint="日曜日" persistent-hint
                                    v-model="form.sunday"></v-switch>

                            </v-list-item>

                        </v-list>





                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="開始" :items=timeList name="start_time"
                                    v-model="form.start_time" :error="Boolean(form.errors.start_time)"
                                    :error-messages="form.errors.start_time" :disabled="form.enabled"
                                    clearable></v-select>
                            </v-list-item>

                        </v-list>

                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="終了時間" :items=timeList name="end_time"
                                    v-model="form.end_time" :error="Boolean(form.errors.end_time)"
                                    :error-messages="form.errors.end_time" :disabled="form.enabled"
                                    clearable></v-select>
                            </v-list-item>

                        </v-list>





                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="error" label="終日" name="enabled"
                                    v-model="form.enabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-pencil" label="種別" name="title_type"
                                    v-model="form.title_type" :items=titleTypeList variant="underlined" clearable
                                    :error="Boolean(form.errors.title_type)"
                                    :error-messages="form.errors.title_type"></v-select>
                            </v-list-item>
                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-text-field prepend-icon="mdi-pencil" name="title" label="タイトル" v-model="form.title"
                                    maxlength="200" :error="Boolean(form.errors.title)"
                                    :error-messages="form.errors.title"></v-text-field>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-textarea prepend-icon="mdi-pencil" name="content" label="内容" v-model="form.content"
                                    :error="Boolean(form.errors.content)"
                                    :error-messages="form.errors.content"></v-textarea>
                            </v-list-item>
                        </v-list>



                    </v-card-text>

                    <v-card-text class="text-center">
                        <Button :small="$vuetify.breakpoint.xs" type="submit" color="primary">
                            <v-icon left>
                                mdi-magnify
                            </v-icon>
                            この内容で作成する
                        </Button>
                    </v-card-text>
                </form>


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

        <!-- イベント追加ダイアログ(PC) -->
        <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '800px' : 'unset'"
            v-if="$vuetify.breakpoint.smAndUp">
            <v-card flat tile>

                <form @submit.prevent="create">
                    <v-card-text>

                        <!-- 日付 -->
                        <v-list v-if="!form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="date" label="日付"
                                    v-model="form.date" :error="Boolean(form.errors.date)"
                                    :error-messages="form.errors.date"></v-text-field>
                            </v-list-item>
                        </v-list>


                        <!-- 期間設定 -->
                        <v-list v-if="form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="start_date" label="開始日"
                                    v-model="form.start_date" :error="Boolean(form.errors.start_date)"
                                    :error-messages="form.errors.start_date"></v-text-field>

                                <v-text-field type="date" name="end_date" label="終了日" v-model="form.end_date"
                                    :error="Boolean(form.errors.end_date)" :error-messages="form.errors.end_date"
                                    class="ml-10"></v-text-field>
                            </v-list-item>
                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="warning" label="期間" name="rangeEnabled"
                                    v-model="form.rangeEnabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list v-if="form.rangeEnabled" style="margin-top: -20px;" class="mb-10">
                            <v-list-item>
                                <v-switch dense filled class="mr-4" color="primary" hint="月曜日" persistent-hint
                                    v-model="form.monday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="火曜日" persistent-hint
                                    v-model="form.tuesday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="水曜日" persistent-hint
                                    v-model="form.wednesday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="木曜日" persistent-hint
                                    v-model="form.thursday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="金曜日" persistent-hint
                                    v-model="form.friday"></v-switch>
                                <v-switch dense filled class="mr-4" color="primary" hint="土曜日" persistent-hint
                                    v-model="form.saturday"></v-switch>
                                <v-switch dense filled color="primary" hint="日曜日" persistent-hint
                                    v-model="form.sunday"></v-switch>

                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="開始" :items=timeList name="start_time"
                                    v-model="form.start_time" :error="Boolean(form.errors.start_time)"
                                    :error-messages="form.errors.start_time" :disabled="form.enabled"
                                    clearable></v-select>

                                <v-select label="終了時間" :items=timeList name="end_time" v-model="form.end_time"
                                    :error="Boolean(form.errors.end_time)" :error-messages="form.errors.end_time"
                                    class="ml-10" :disabled="form.enabled" clearable></v-select>

                            </v-list-item>

                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="error" label="終日" name="enabled"
                                    v-model="form.enabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select label="種別" name="title_type" v-model="form.title_type" :items=titleTypeList
                                    variant="underlined" style="width: 25%;" clearable
                                    :error="Boolean(form.errors.title_type)"
                                    :error-messages="form.errors.title_type"></v-select>

                                <v-text-field name="title" label="タイトル" v-model="form.title" maxlength="200"
                                    :error="Boolean(form.errors.title)" :error-messages="form.errors.title"
                                    class="ml-10" style="width: 70%;"></v-text-field>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-textarea name="content" label="内容" v-model="form.content"
                                    :error="Boolean(form.errors.content)"
                                    :error-messages="form.errors.content"></v-textarea>
                            </v-list-item>
                        </v-list>



                    </v-card-text>

                    <v-card-text class="text-center">
                        <Button :small="$vuetify.breakpoint.xs" type="submit" color="primary">
                            <v-icon left>
                                mdi-magnify
                            </v-icon>
                            この内容で作成する
                        </Button>
                    </v-card-text>
                </form>


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


        <!-- イベント追加ダイアログ(PC) -->
        <v-dialog v-model="displaySchedule" :max-width="$vuetify.breakpoint.smAndUp ? '800px' : 'unset'"
            v-if="$vuetify.breakpoint.smAndUp">
            <v-card flat tile>

                <form @submit.prevent="update">
                    <v-card-text>



                        <!-- 日付 -->
                        <v-list v-if="!edit_form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="date" label="日付"
                                    v-model="edit_form.date" :error="Boolean(edit_form.errors.date)"
                                    :error-messages="edit_form.errors.date"></v-text-field>
                            </v-list-item>
                        </v-list>





                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="開始" :items=timeList name="start_time"
                                    v-model="edit_form.start_time" :error="Boolean(edit_form.errors.start_time)"
                                    :error-messages="edit_form.errors.start_time" :disabled="edit_form.enabled"
                                    clearable></v-select>

                                <v-select label="終了時間" :items=timeList name="end_time" v-model="edit_form.end_time"
                                    :error="Boolean(edit_form.errors.end_time)"
                                    :error-messages="edit_form.errors.end_time" class="ml-10"
                                    :disabled="edit_form.enabled" clearable></v-select>

                            </v-list-item>

                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="error" label="終日" name="enabled"
                                    v-model="edit_form.enabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select label="種別" name="title_type" v-model="edit_form.title_type"
                                    :items=titleTypeList variant="underlined" style="width: 25%;" clearable
                                    :error="Boolean(form.errors.title_type)"
                                    :error-messages="form.errors.title_type"></v-select>

                                <v-text-field name="title" label="タイトル" v-model="edit_form.title" maxlength="200"
                                    :error="Boolean(edit_form.errors.title)" :error-messages="edit_form.errors.title"
                                    class="ml-10" style="width: 70%;"></v-text-field>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-textarea name="content" label="内容" v-model="edit_form.content"
                                    :error="Boolean(edit_form.errors.content)"
                                    :error-messages="edit_form.errors.content"></v-textarea>
                            </v-list-item>
                        </v-list>



                    </v-card-text>

                    <v-card-text class="text-center">
                        <Button :small="$vuetify.breakpoint.xs" type="submit" color="primary">
                            <v-icon left>
                                mdi-magnify
                            </v-icon>
                            この内容で更新する
                        </Button>
                    </v-card-text>


                    <v-card-text class="text-right">
                        <Button color="error" :small="$vuetify.breakpoint.xs" type="button"
                            @click.native="deleteSchedule(edit_form.id)">
                            <v-icon>
                                mdi-delete-outline
                            </v-icon>
                            削除
                        </Button>
                    </v-card-text>


                </form>


                <v-divider></v-divider>

                <v-row>
                    <v-card-text class="text-right">
                        <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="displaySchedule = false">
                            <v-icon>
                                mdi-close
                            </v-icon>
                            閉じる
                        </Button>
                    </v-card-text>

                </v-row>



            </v-card>
        </v-dialog>




        <!-- イベント追加ダイアログ(スマホ) -->
        <v-dialog v-model="displaySchedule" :max-width="$vuetify.breakpoint.smAndUp ? '800px' : 'unset'"
            v-if="!$vuetify.breakpoint.smAndUp">
            <v-card flat tile>

                <form @submit.prevent="update">
                    <v-card-text>



                        <!-- 日付 -->
                        <v-list v-if="!edit_form.rangeEnabled">
                            <v-list-item>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="date" label="日付"
                                    v-model="edit_form.date" :error="Boolean(edit_form.errors.date)"
                                    :error-messages="edit_form.errors.date"></v-text-field>
                            </v-list-item>
                        </v-list>





                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="開始" :items=timeList name="start_time"
                                    v-model="edit_form.start_time" :error="Boolean(edit_form.errors.start_time)"
                                    :error-messages="edit_form.errors.start_time" :disabled="edit_form.enabled"
                                    clearable></v-select>

                            </v-list-item>

                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-clock-outline" label="終了時間" :items=timeList name="end_time"
                                    v-model="edit_form.end_time" :error="Boolean(edit_form.errors.end_time)"
                                    :error-messages="edit_form.errors.end_time" :disabled="edit_form.enabled"
                                    clearable></v-select>

                            </v-list-item>

                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-switch dense filled class="mt-0" color="error" label="終日" name="enabled"
                                    v-model="edit_form.enabled"></v-switch>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>
                                <v-select prepend-icon="mdi-pencil" label="種別" name="title_type"
                                    v-model="edit_form.title_type" :items=titleTypeList variant="underlined" clearable
                                    :error="Boolean(form.errors.title_type)"
                                    :error-messages="form.errors.title_type"></v-select>

                            </v-list-item>
                        </v-list>


                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-text-field prepend-icon="mdi-pencil" name="title" label="タイトル"
                                    v-model="edit_form.title" maxlength="200" :error="Boolean(edit_form.errors.title)"
                                    :error-messages="edit_form.errors.title"></v-text-field>
                            </v-list-item>
                        </v-list>



                        <v-list style="margin-top: -20px;">
                            <v-list-item>

                                <v-textarea prepend-icon="mdi-pencil" name="content" label="内容"
                                    v-model="edit_form.content" :error="Boolean(edit_form.errors.content)"
                                    :error-messages="edit_form.errors.content"></v-textarea>
                            </v-list-item>
                        </v-list>



                    </v-card-text>

                    <v-card-text class="text-center">
                        <Button :small="$vuetify.breakpoint.xs" type="submit" color="primary">
                            <v-icon left>
                                mdi-magnify
                            </v-icon>
                            この内容で更新する
                        </Button>
                    </v-card-text>


                    <v-card-text class="text-right">
                        <Button color="error" :small="$vuetify.breakpoint.xs" type="button"
                            @click.native="deleteSchedule(edit_form.id)">
                            <v-icon>
                                mdi-delete-outline
                            </v-icon>
                            削除
                        </Button>
                    </v-card-text>


                </form>


                <v-divider></v-divider>

                <v-row>
                    <v-card-text class="text-right">
                        <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="displaySchedule = false">
                            <v-icon>
                                mdi-close
                            </v-icon>
                            閉じる
                        </Button>
                    </v-card-text>

                </v-row>

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

// console.log(this.schedule);

const dateString = '';

export default {
    components: { Layout, Link, FullCalendar },

    props: ['schedule', 'user', 'loginUser', 'userList', 'timeList', 'titleTypeList'],

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
                enabled: false,
                monday: true,
                tuesday: true,
                wednesday: true,
                thursday: true,
                friday: true,
                saturday: true,
                sunday: true,
                route: 1,
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
                route: 1,
            }),

            delete_form: this.$inertia.form({
                id: undefined,
            }),

            form_user_id: this.user.id,


            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                locales: [jaLocale],
                locale: "ja",
                businessHours: "true",
                height: "auto",
                firstDay: 1,
                weekends: "true",
                dateClick: this.handleDateClick,
                events: this.schedule,
                eventClick: this.handleEventClick,
                nowIndicator: true,
                headerToolbar: {
                    // title, prev, next, prevYear, nextYear, today
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth timeGridWeek'
                },
                eventDidMount: this.handleEventDidMount,
                eventMouseEnter: this.handleEventMouseEnter,
                scrollTime: '08:00:00',
                contentHeight: 'auto',
                // resourceAreaWidth:'25%',
                // eventTimeFormat: { hour: 'numeric', minute: '2-digit' }
            },


            calendarOptionsSmartphone: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridWeek',
                locales: [jaLocale],
                locale: "ja",
                businessHours: "true",
                height: "300px",
                firstDay: "1",
                weekends: "true",
                // dateClick: this.handleDateClick,
                events: this.schedule,
                eventClick: this.handleEventClick,
                headerToolbar: {
                    left: 'prev,next today',
                    center: '',
                    right: '',

                },
                eventDidMount: this.handleEventDidMount,
                eventMouseEnter: this.handleEventMouseEnter,
                stickyHeaderDates: "false",
                titleFormat: { month: "2-digit" },
            },
            loading: {},
        }
    },

    methods: {
        handleDateClick: function (info) {

            if (this.loginUser.id != this.user.id) {
                return false;
            }

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

            if (this.loginUser.id != this.user.id) {
                return false;
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
                    this.edit_form.title_type = res.data.title_type ?? null;
                    this.edit_form.enabled = res.data.start_time ? false : true;
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

        handleDateClickSmartphone: function () {
            // alert();
            if (this.loginUser.id == this.user.id) {
                this.showDialog = true;
            }

        },

        handleEventMouseEnter(e) {
            console.log(e.event),
                tippy(e.el, {
                    content: `<div class="mx-10">
                    <h4 style="text-decoration:underline;">${e.event.extendedProps.pops_time}</h4>
                 <p style="font-weight: bold;font-size:larger:">${e.event.extendedProps.pops_tile}<p>
                 <p>${e.event.extendedProps.content}<p>
                </div>`,
                    allowHTML: true,
                    theme: 'light-border'
                });
        },


        handleEventDidMount(e) {
            // tippy(e.el, {
            //     content: `<div style="">
            //      ${e.event.title}<br/>
            //      <p>${e.event.extendedProps.content}<p><br/>
            //     </div>`,
            //     allowHTML: true,
            //     theme: 'light-border'
            // });
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
        deleteSchedule: function (id) {
            this.$confirm('スケジュールを削除してもよろしいでしょうか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
                if (isAccept) {
                    //削除処理
                    this.delete_form.delete(this.$route('schedule.destroy', { schedule: id }), {
                        preserveState: (page) => Object.keys(page.props.errors).length,
                        onStart: () => this.$set(this.loading, "delete", true),
                        onSuccess: () => this.$toasted.show('削除しました'),
                        onFinish: () => this.$set(this.loading, "delete", false),

                    })
                }
            })
        },


        changeUser: function () {
            alert(this.form_user_id);
        },
    },


}
</script>