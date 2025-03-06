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


.fc-header-toolbar {
    margin-bottom: 0;
}

.fc-button {
    padding: 0 10px;
}
</style>

<template>
    <Layout>


        <v-row>
            <v-col cols="3">
                <v-select dense filled v-model="selectDepartment" :items="departmentList" item-value="department"
                    item-text="department" @change="changeDepartment" multiple></v-select>
            </v-col>
        </v-row>


        <FullCalendar class='p-5' :options='calendarOptions' ref="fullCalendar" id="calendar_frame">
        </FullCalendar>


        <!-- 新規登録モーダル -->
        <v-dialog v-model="detailDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">

            <v-card>
                <v-card-text class="px-10 py-6">

                    <v-row>
                        <v-col cols="12" class="text-right">
                            <v-icon @click.native="detailDialog = false;">mdi-close</v-icon>
                        </v-col>
                    </v-row>

                    <v-row style="margin-top: -10px;">
                        <v-col cols="1"><v-icon>mdi-checkbox-blank</v-icon></v-col>

                        <v-col cols="11" style="font-size: x-large;">

                            {{ form.title }} <v-chip v-if="form.category">{{ form.category }}</v-chip>
                        </v-col>
                    </v-row>


                    <v-row>
                        <v-col cols="1"><v-icon>mdi-calendar-clock</v-icon></v-col>
                        <v-col cols="11">
                            <span style="font-size: large;">{{ form.date }}</span>
                            <template v-if="!form.all_day"> ({{ form.start_time }} - {{ form.end_time }})</template>
                        </v-col>
                    </v-row>


                    <v-row v-if="form.content">
                        <v-col cols="1"><v-icon>mdi-format-float-left</v-icon></v-col>
                        <v-col cols="11">
                            <span style="white-space: pre-line;">{{ form.content }}</span>
                        </v-col>
                    </v-row>


                    <v-row v-if="form.booking.room">
                        <v-col cols="1"><v-icon>mdi-calendar-check</v-icon></v-col>
                        <v-col cols="11">{{ form.booking.room.name ?? '-' }}</v-col>
                    </v-row>


                    <v-row v-if="form.guestUsers.length > 0">
                        <v-col cols="1"><v-icon>mdi-account-multiple</v-icon></v-col>
                        <v-col cols="11">
                            <template v-for="guest in form.guestUsers">
                                {{ guest.user.name ?? '-' }} /
                            </template>
                        </v-col>
                    </v-row>

                    <v-row v-if="form.zoomUrl">
                        <v-col cols="1"><v-icon>mdi-record</v-icon></v-col>
                        <v-col cols="11">
                            <span v-html="autoLink(form.zoomUrl)" style="font-size: small;"></span></v-col>
                    </v-row>


                    <v-row v-if="form.user">
                        <v-col cols="1"><v-icon>mdi-account</v-icon></v-col>
                        <v-col cols="11">{{ form.user.name ?? '-' }}</v-col>
                    </v-row>

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
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';


export default {
    components: { Layout, Link, FullCalendar },

    props: ['scheduleData', 'users', 'selectDepartment', 'departmentList'],

    data() {

        var resourcesData = [];
        var eventsData = [];

        this.users.forEach(function (user) {
            console.log(user);
            resourcesData.push(user);
        })

        this.scheduleData.forEach(function (schedule) {
            console.log(schedule);
            eventsData.push(schedule);
        })



        var calendarOptions = {
            plugins: [resourceTimelinePlugin],
            initialView: "resourceTimelineMonth",
            // resourceLabelText:"...",
            resourceAreaHeaderContent: '',
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            height: "auto",
            firstDay: "1",
            slotMinTime: "08:00:00",
            slotMaxTime: "24:00:00",
            defaultTimedEventDuration: "08:00:00",
            scrollTime: "09:00:00",
            weekends: "true",
            headerToolbar: {
                right: 'prev,next',
                left: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth',
                center: 'title',
            },
            resources: resourcesData,
            resourceAreaWidth: '10%',
            events: eventsData,
            resourceOrder: 'type1,type2',
            eventClick: this.handleEventClick,
        };


        return {

            calendarOptions: calendarOptions,

            detailDialog: false,

            form: this.$inertia.form({
                id: undefined,
                type: 1,
                user: {},
                date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                end_date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                start_time: '09:00',
                end_time: '10:00',
                title: '',
                category: null,
                content: null,
                all_day: false,
                is_public: 0,
                guest: [],
                room: 0,
                booking: [],
                guestUsers: [],
                zoomUrl: undefined,
                zoomUrlId: undefined,
            }),
        }

    },

    mounted() {
    },

    methods: {

        //イベント選択
        handleEventClick: function (eventObj, el) {

            //初期化
            this.form.id = undefined;
            this.form.user = {};
            this.form.start_time = '09:00';
            this.form.end_time = '10:00';
            this.form.title = '';
            this.form.category = null;
            this.form.content = null;
            this.form.all_day = false;
            this.form.is_public = 0;
            this.form.is_visual_room = false;
            this.form.is_visual_geust = false;
            this.form.is_visual_detail = false;
            this.form.is_visual_meeting = false;
            this.form.booking = [];
            this.form.guest = [];
            this.form.guestUsers = [];
            this.form.is_finish = 0;
            this.form.type = 1;
            this.form.zoomUrl = undefined;
            this.form.zoomUrlId = undefined;


            axios.get(`/api/schedule/select_data/${eventObj.event.id}`)
                .then((res) => {

                    var schedule = res.data.schedule;
                    var booking = res.data.booking;
                    var guest = res.data.guest;

                    var users = res.data.users;

                    this.form.id = schedule.id;
                    this.form.title = schedule.title;


                    this.form.date = schedule.date;
                    this.form.end_date = schedule.end_date;
                    this.form.start_time = schedule.start_time;
                    this.form.end_time = schedule.end_time;
                    this.form.user = schedule.user;

                    this.form.category = schedule.category ?? null;
                    this.form.is_public = schedule.is_public;
                    this.form.all_day = schedule.all_day;

                    this.form.zoomUrl = schedule.zoom_url;
                    this.form.zoomUrlId = schedule.zoom_id;

                    //施設予約
                    if (booking) {
                        this.form.booking = booking;
                        this.form.room = booking.room_id;
                        this.form.is_visual_room = true;
                    }

                    //ゲスト
                    if (guest.length > 0) {
                        this.form.guest = guest;
                        this.form.is_visual_geust = true;
                    }

                    if (users) {
                        this.form.guestUsers = users;
                    }

                    // 詳細記載
                    if (schedule.content) {
                        this.form.content = schedule.content;
                        this.form.is_visual_detail = true;
                    }

                    console.log(this.form);

                    this.detailDialog = true;

                })
                .catch((e) => {

                })
                .finally(() => {

                });
        },


        //部署変更
        changeDepartment() {

            if (this.selectDepartment.length == 0) {
                return false;
            }

            var department = '';
            this.selectDepartment.forEach(function (dep) {
                department += dep + ',';
            })


            window.location.href = `/list/schedule?department=${department}`;

        },

        autoLink(text) {
            return _.isString(text) ? text.replace(/(https?:\/\/[^\s]*)/g, "<a href='$1' target='_blank'>$1</a>") : '';
        },
    },

}
</script>