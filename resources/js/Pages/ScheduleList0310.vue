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
            <v-col cols="2">
                <v-select dense filled v-model="department" :items="department_list" item-value="department" item-text="department" @change="changeDepartment"></v-select>
            </v-col>
            <v-col cols="10">
                <v-select dense filled v-model="form.user_id" :items="member_list" item-value="id" item-text="name"
                    @change="changeUser"></v-select>
            </v-col>
        </v-row>



        <FullCalendar class='p-5' :options='calendarOptions' ref="fullCalendar" id="calendar_frame"
            v-if="$vuetify.breakpoint.smAndUp && display">
        </FullCalendar>



        <FullCalendar :options='calendarOptionsSmartphone' ref="fullCalendar" v-if="!$vuetify.breakpoint.smAndUp">
        </FullCalendar>



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



export default {
    components: { Layout, Link, FullCalendar },

    props: ['schedule_list', 'user', 'loginUser', 'member_list', 'department','department_list'],

    data() {

        var schedule = [];


        var calendarOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: "timeGridWeek",
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            height: "auto",
            firstDay: "1",
            slotMinTime: "07:00:00",
            slotMaxTime: "22:00:00",
            defaultTimedEventDuration: "08:00:00",
            scrollTime: "09:00:00",
            weekends: "true",
            events: this.schedule,
            headerToolbar: {
                left: '',
                center: '',
                right: 'prev,next today',
            },
        };

        var calendarOptionsSmartphone = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            height: "auto",
            firstDay: "1",
            weekends: "true",
            events: this.schedule,
            headerToolbar: {
                left: '',
                center: 'title',
                right: 'prev,next today',
            },
        };

        return {

            form: {
                user_id: this.user.id,
            },

            calendarOptions: calendarOptions,

            calendarOptions: calendarOptionsSmartphone,


            loading: {},
            display: false,
        }
    },

    mounted() {
        console.log('uuu');
        console.log(this.schedule_list);
        console.log(this.schedule_list[this.form.user_id]);
        this.schedule = this.schedule_list[this.form.user_id];

        this.calendarOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: "timeGridWeek",
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            height: "auto",
            firstDay: "1",
            slotMinTime: "07:00:00",
            slotMaxTime: "22:00:00",
            defaultTimedEventDuration: "08:00:00",
            scrollTime: "09:00:00",
            weekends: "true",
            events: this.schedule,
            headerToolbar: {
                left: '',
                center: '',
                right: 'prev,next today',
            },
        };


        this.calendarOptionsSmartphone = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            locales: [jaLocale],
            locale: "ja",
            businessHours: "true",
            height: "auto",
            firstDay: "1",
            weekends: "true",
            events: this.schedule,
            headerToolbar: {
                left: '',
                center: 'title',
                right: 'prev,next today',
            },
        };

        console.log(this.schedule);
        this.display = true;

        // FullCalendar.fullCalendar.render();
        // calendarApi.next();

    },

    methods: {
        handleEventMouseEnter(e) {
            console.log(e.event),
                tippy(e.el, {
                    content: `<div">
                    <p>${e.event.extendedProps.pops_time}<p>
                 <p style="white-space:pre-wrap;">${e.event.extendedProps.pops_tile}<p>
                 <p style="white-space:pre-wrap;">${e.event.extendedProps.content}<p>
                </div>`,
                    allowHTML: true,
                    theme: 'light-border'
                });
        },

        changeUser() {
            console.log(this.form.user_id);


            this.schedule = this.schedule_list[this.form.user_id];

            console.log(this.schedule);


            this.calendarOptions = {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: "timeGridWeek",
                locales: [jaLocale],
                locale: "ja",
                businessHours: "true",
                height: "auto",
                firstDay: "1",
                slotMinTime: "07:00:00",
                slotMaxTime: "22:00:00",
                defaultTimedEventDuration: "08:00:00",
                scrollTime: "09:00:00",
                weekends: "true",
                events: this.schedule,
                headerToolbar: {
                    left: '',
                    center: '',
                    right: 'prev,next today',
                },
            };

            this.calendarOptionsSmartphone = {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                locales: [jaLocale],
                locale: "ja",
                businessHours: "true",
                height: "auto",
                firstDay: "1",
                weekends: "true",
                events: this.schedule,
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: 'prev,next today',
                },
            };
        },


        changeDepartment(){
            console.log(this.department);
            window.location.href = `/redirect/schedule/${this.department}`;
        },

    },

}
</script>