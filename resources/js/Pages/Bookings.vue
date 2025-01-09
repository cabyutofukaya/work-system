<template>
    <Layout>
        <!-- PC -->
        <v-card flat tile v-if="$vuetify.breakpoint.smAndUp">

            <v-row class="mt-5">

                <Button color="#3a875d" class="ml-5 py-3" v-if="room_id == 0">
                    全体
                </Button>

                <Button color="#CCCCCC" class="ml-5 py-3" @click.native="clickRoom(0)" v-if="room_id != 0">
                    全体
                </Button>

                <div cols="2" v-for="room in rooms" :key="room.id">

                    <!-- 現在の表示施設 -->
                    <Button :color="room.color" class="ml-5 py-3" v-if="room.id == room_id">
                        {{ room.name }}
                    </Button>

                    <!-- 現在の非表示施設 -->
                    <Button color="#CCCCCC" class="ml-5 py-3" @click.native="clickRoom(room.id)"
                        v-if="room.id != room_id">
                        {{ room.name }}
                    </Button>

                </div>

                <!-- <div class="ml-5">
                    <v-icon @click.native="infoImageDialog = true">mdi-information-outline</v-icon>
                </div> -->

            </v-row>


            <FullCalendar :options='calendarOptions' ref="fullCalendar" style="max-width: 1000px;" id="calendar">
            </FullCalendar>


        </v-card>



        <!-- スマホ -->
        <v-card flat tile v-if="!$vuetify.breakpoint.smAndUp">


            <v-row class="mt-5 mb-10">

                <Button color="#3a875d" class="ml-5 py-3" v-if="room_id == 0">
                    全体
                </Button>

                <Button color="#CCCCCC" class="ml-5 py-3" @click.native="clickRoom(0)" v-if="room_id != 0">
                    全体
                </Button>


                <div cols="2" v-for="room in rooms" :key="room.id">

                    <!-- 現在の表示施設 -->
                    <Button :color="room.color" class="ml-5 py-3" v-if="room.id == room_id">
                        {{ room.name }}
                    </Button>

                    <!-- 現在の非表示施設 -->
                    <Button color="#CCCCCC" class="ml-5 py-3" @click.native="clickRoom(room.id)"
                        v-if="room.id != room_id">
                        {{ room.name }}
                    </Button>

                </div>

                <div class="ml-5">
                    <v-icon @click.native="infoImageDialog = true">mdi-information-outline</v-icon>
                </div>

            </v-row>


            <div style="min-width: 600px;" class="mt-20">
                <template>
                    <FullCalendar :options='calendarOptionsSmartphone' ref="fullCalendar"
                        style="min-width: 500px;max-width: 700px;">
                    </FullCalendar>
                </template>
            </div>


            <!-- ページトップスクロールボタン -->
            <transition name="scroll-top-btn">
                <v-btn fab dark fixed bottom right class="mb-10" color="info" style="margin-bottom: 100px;"
                    @click="this.handleDateClickSmartphone">
                    <v-icon large>mdi-plus</v-icon>
                </v-btn>
            </transition>





        </v-card>





        <!-- 新規施設ダイアログ -->
        <v-dialog v-model="createDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : '380px'"
            @click:outside="closeCreateDialog">
            <v-card flat tile>
                <v-card-title>
                    新規施設予約
                </v-card-title>

                <form @submit.prevent="store">
                    <v-card-text>
                        <v-list>

                            <!-- 会議室種別 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="会議室種別"
                                    class="mx-5" persistent-hint name="room_id" v-model="form.room_id"
                                    :error="Boolean(form.errors.room_id)" :items="this.roomType" item-value="id"
                                    item-text="name" :error-messages="form.errors.room_id"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>

                            <!-- 日付 -->
                            <v-list-item class="mb-4">
                                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="開始日"
                                    class="mx-5" persistent-hint type="date" name="started_at" v-model="form.started_at"
                                    maxlength="200" :error="Boolean(form.errors.started_at)"
                                    :error-messages="form.errors.started_at"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-text-field>

                            </v-list-item>


                            <!-- 開始時間 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="開始時間"
                                    class="mx-5" persistent-hint name="started_time" v-model="form.started_time"
                                    :error="Boolean(form.errors.started_time)" :items="this.startedAtList"
                                    :error-messages="form.errors.started_time"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>


                            <!-- 時間 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="利用時間(分)"
                                    class="mx-5" persistent-hint name="time" v-model="form.time"
                                    :error="Boolean(form.errors.time)" :items="this.timeList"
                                    :error-messages="form.errors.time" :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>


                            <!-- 内容 -->
                            <v-list-item class="mb-4">
                                <v-textarea dense filled clearable prepend-inner-icon="mdi-pencil" label="内容"
                                    class="mx-5" persistent-hint name="title" v-model="form.title" rows="3"
                                    :error="Boolean(form.errors.title)" :error-messages="form.errors.title"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-textarea>

                            </v-list-item>

                        </v-list>
                    </v-card-text>

                    <v-card-text class="text-center">
                        <Button :small="$vuetify.breakpoint.xs" type="submit">
                            予約する
                        </Button>
                    </v-card-text>
                </form>

                <v-divider></v-divider>


                <v-card-text class="text-right">
                    <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="createDialog = false">
                        <v-icon>
                            mdi-close
                        </v-icon>
                        閉じる
                    </Button>
                </v-card-text>
            </v-card>
        </v-dialog>





        <!-- 詳細施設予約ダイアログ -->
        <v-dialog v-model="updateDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : '390px'"
            @click:outside="closeUpdateDialog">
            <v-card flat tile>
                <v-card-title>
                    施設予約詳細
                </v-card-title>

                <form @submit.prevent="update">
                    <v-card-text>
                        <v-list>


                            <!-- 予約者 -->
                            <v-list-item class="mb-4">
                                <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="予約者" class="mx-5"
                                    persistent-hint name="user_name" v-model="form.user_name" readonly></v-text-field>
                            </v-list-item>

                            <!-- 会議室種別 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="会議室種別"
                                    class="mx-5" persistent-hint name="room_id" v-model="form.room_id"
                                    :error="Boolean(form.errors.room_id)" :items="this.roomType" item-value="id"
                                    item-text="name" :error-messages="form.errors.room_id"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>

                            <!-- 日付 -->
                            <v-list-item class="mb-4">
                                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="開始日"
                                    class="mx-5" persistent-hint type="date" name="started_at" v-model="form.started_at"
                                    maxlength="200" :error="Boolean(form.errors.started_at)"
                                    :error-messages="form.errors.started_at"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-text-field>

                            </v-list-item>


                            <!-- 開始時間 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="開始時間"
                                    class="mx-5" persistent-hint name="started_time" v-model="form.started_time"
                                    :error="Boolean(form.errors.started_time)" :items="this.startedAtList"
                                    :error-messages="form.errors.started_time"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>


                            <!-- 時間 -->
                            <v-list-item class="mb-4">
                                <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="利用時間"
                                    class="mx-5" persistent-hint name="time" v-model="form.time"
                                    :error="Boolean(form.errors.time)" :items="this.timeList"
                                    :error-messages="form.errors.time" :autofocus="!$vuetify.breakpoint.xs"></v-select>

                            </v-list-item>


                            <!-- 内容 -->
                            <v-list-item class="mb-4">
                                <v-textarea dense filled clearable prepend-inner-icon="mdi-pencil" label="内容"
                                    class="mx-5" persistent-hint name="title" v-model="form.title" rows="2"
                                    :error="Boolean(form.errors.title)" :error-messages="form.errors.title"
                                    :autofocus="!$vuetify.breakpoint.xs"></v-textarea>

                            </v-list-item>

                        </v-list>
                    </v-card-text>

                    <v-row v-if="this.user.id == this.user_id" style="margin-top:-50px">
                        <v-col>
                            <v-card-text class="text-center">
                                <Button :small="$vuetify.breakpoint.xs" type="submit" color="primary">
                                    予約情報を更新する
                                </Button>
                            </v-card-text>
                        </v-col>
                    </v-row>

                    <v-row style="margin-top:-40px">
                        <v-col>
                            <v-card-text class="text-right">
                                <Button :small="$vuetify.breakpoint.xs" type="button" color="danger"
                                    @click.native="deleteBooking">
                                    削除する
                                </Button>
                            </v-card-text>
                        </v-col>
                    </v-row>


                </form>

                <v-divider></v-divider>


                <v-card-text class="text-right">
                    <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="updateDialog = false">
                        <v-icon>
                            mdi-close
                        </v-icon>
                        閉じる
                    </Button>
                </v-card-text>
            </v-card>
        </v-dialog>




        <!-- 詳細施設写真ダイアログ -->
        <!-- <v-dialog v-model="infoImageDialog" :max-width="$vuetify.breakpoint.smAndUp ? '1000px' : 'unset'"
            @click:outside="infoImageDialog = false">
            <v-card flat tile>
                <img :src="`/storage/booking/floormap.png`" alt="" class="c-img" width="auto"
                    v-if="$vuetify.breakpoint.smAndUp">
                <img :src="`/storage/booking/floormap.png`" alt="" class="c-img" max-width="350px" height="auto"
                    v-if="!$vuetify.breakpoint.smAndUp">

            </v-card>
        </v-dialog> -->


    </Layout>
</template>


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

.calendar {
    height: 20%;
    width: 80%;
    margin-bottom: 80px;
}

.fc-day-sat {
    background-color: #eaf4ff;
}

.fc-day-sun {
    background-color: #ffeaea;
}

/* .fc-header-toolbar {
    margin-bottom: 0;
} */


.fc .fc-button .fc-icon {
    font-size: 1.5em;
    vertical-align: middle;
    margin-top: -10px;
}

/* #calendar .fc-button {
    padding: 2px 24px;
}

.fc .fc-toolbar-title {
    font-size: 1.2em;
} */
</style>


<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import jaLocale from '@fullcalendar/core/locales/ja'





export default {
    components: { Layout, Link, FullCalendar },

    props: ['bookings', 'rooms', 'room_id', 'user', 'roomType'],

    data() {


        let calendarOptions = {
            plugins: [timeGridPlugin, interactionPlugin],
            initialView: 'timeGridWeek',
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            firstDay: "1",
            weekends: "true",
            height: "auto",
            width: "auto",
            events: this.bookings,
            headerToolbar: {
                left: '',
                center: '',
                right: 'prev,next today',
            },
            allDaySlot: false,
            slotMinTime: "07:00:00",
            slotMaxTime: "21:00:00",
            defaultTimedEventDuration: "09:00:00",
            weekends: true,
            eventClick: this.handleEventClick,
            dateClick: this.clickCalendar,
            eventDrop: this.dropEventData,
            eventResize:this.dropEventData,
            droppable: "true",
            editable: "true",
            selectable: "true",
        }

        let calendarOptionsSmartphone = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'timeGridWeek',
            locales: [jaLocale],
            // locale: "ja",
            businessHours: "true",
            firstDay: "1",
            weekends: "true",
            height: "auto",
            width: "auto",
            events: this.bookings,
            headerToolbar: {
                left: 'prev,next',
                center: '',
                right: '',
            },
            allDaySlot: false,
            slotMinTime: "07:00:00",
            slotMaxTime: "21:00:00",
            defaultTimedEventDuration: "09:00:00",
            weekends: true,
            eventClick: this.handleEventClick,
            // dateClick: this.clickCalendar,
        }

        return {

            createDialog: false,
            updateDialog: false,
            infoImageDialog: false,

            user_id: undefined,

            // Inertia Formヘルパ
            form: this.$inertia.form('BookingCreate', {
                id: undefined,
                started_at: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                started_time: undefined,
                time: undefined,
                title: undefined,
                room_id: this.room_id == 0 ? 1 : this.room_id,
                user_name: undefined,
            }),

            startedAtList: ['07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00'],

            timeList: [30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330, 360, 390],

            calendarOptions,
            calendarOptionsSmartphone,


            loading: {},
        }
    },

    methods: {
        clickRoom(id) {
            if (id == 0) {
                window.location.href = `/bookings`;
            } else {
                window.location.href = `/bookings?room=${id}`;
            }

        },

        //イベントクリック
        handleEventClick: function (eventObj, el) {

            console.log(eventObj);

            axios.get(`/bookings/${eventObj.event.id}`)
                .then((res) => {

                    console.log(res.data);

                    this.form.id = res.data.id;
                    this.form.started_at = res.data.started_at;
                    this.form.started_time = res.data.started_time;
                    this.form.time = res.data.time;
                    this.form.title = res.data.title;
                    this.form.room_id = Number(res.data.room_id);

                    console.log(res.data.user.name);

                    this.form.user_name = res.data.user.name;

                    this.user_id = res.data.user_id;

                    console.log(this.form);


                    this.updateDialog = true;

                })
                .catch((e) => {

                })
                .finally(() => {

                });

        },

        //カレンダークリック
        clickCalendar: function (eventObj, el) {


            var dateformat = eventObj.dateStr;
            var d = new Date(dateformat);


            var year = d.getFullYear();

            var month = ('00' + (d.getMonth() + 1)).slice(-2);
            var date = ('00' + d.getDate()).slice(-2);


            var hour = ('00' + d.getHours()).slice(-2);
            var minute = ('00' + d.getMinutes()).slice(-2);

            var time = `${hour}:${minute}`;
            var format_date = `${year}-${month}-${date}`;

            this.form.started_at = format_date;
            this.form.started_time = time;

            this.createDialog = true;
        },

        store: function () {
            this.form.post(this.$route('bookings.store'), {
                preserveState: (page) => Object.keys(page.props.errors).length,
                onStart: () => this.$set(this.loading, "store", true),
                onSuccess: () => this.$toasted.show('施設予約しました'),
                onFinish: () => this.$set(this.loading, "store", false),
            }
            )
        },


        update: function () {
            this.form.put(this.$route('bookings.update', this.form.id), {
                preserveState: (page) => Object.keys(page.props.errors).length,
                onStart: () => this.$set(this.loading, "update", true),
                onSuccess: () => this.$toasted.show('施設予約しました'),
                onFinish: () => this.$set(this.loading, "update", false),
            }
            )
        },

        deleteBooking: function () {
            this.form.delete(this.$route('bookings.destroy', this.form.id), {
                preserveState: (page) => Object.keys(page.props.errors).length,
                onStart: () => this.$set(this.loading, "delete", true),
                onSuccess: () => this.$toasted.show('施設予約を削除しました'),
                onFinish: () => this.$set(this.loading, "delete", false),
            }
            )
        },

        clickInfo: function () {

        },


        closeCreateDialog() {
            this.createDialog = false;
        },

        closeUpdateDialog() {
            this.updateDialog = false;
        },

        handleDateClickSmartphone() {
            this.form.started_at = undefined;
            this.form.started_time = undefined;

            this.createDialog = true;
        },

        dropEventData: function (info) {

            var booking_id = info.event.id;
            var start_time = info.event.start;
            var end_time = info.event.end;

            //データ更新処理
            axios.post('/booking/update-time', {
                booking_id: booking_id,
                start_time: start_time,
                end_time: end_time,
            })
                .then((res) => {
                    if(res.data == false){
                        alert('時間を変更に失敗しました');

                    }else{
                        alert('時間を変更しました。');
                    }

                })
                .catch((e) => {
                    console.log(res.data);
                    alert('時間を変更に失敗しました');
                })
                .finally(() => {
                });
        },
    },
    created() {
    }

}
</script>