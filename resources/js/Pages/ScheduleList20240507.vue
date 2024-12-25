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
        </v-row>


        <FullCalendar class='p-5' :options='calendarOptions' ref="fullCalendar" id="calendar_frame">
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
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';


export default {
    components: { Layout, Link, FullCalendar },

    props: ['scheduleData','users','department','department_list'],

    data() {

        var resourcesData = [];

        var eventsData = [];

        this.users.forEach(function(user){
            console.log(user);
            resourcesData.push(user);
        })

        this.scheduleData.forEach(function(schedule){
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
            firstDay: "20",
            slotMinTime: "06:00:00",
            slotMaxTime: "22:00:00",
            defaultTimedEventDuration: "08:00:00",
            scrollTime: "09:00:00",
            weekends: "true",
            headerToolbar: {
                right: 'prev,next',
                left: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth',
                center: 'title',
            },
            resources: resourcesData,
            resourceAreaWidth:'10%',
            events:eventsData,
            resourceOrder: 'type1,type2',
            eventMouseEnter: this.handleEventMouseEnter,
               
        };


        return {

            calendarOptions: calendarOptions,

        }
    },

    mounted() {
    },

    methods: {
        handleEventMouseEnter(e) {
            console.log(e.event),
                tippy(e.el, {
                    content: `<div">
                    <p>${e.event.extendedProps.pops_time}<p>
                 <p style="white-space:pre-wrap;">${e.event.title}<p>
                <p style="white-space:pre-wrap;">${e.event.extendedProps.content}<p>
                </div>`,
                    allowHTML: true,
                    theme: 'light-border'
                });
        },

        changeDepartment(){
            console.log(this.department);
            window.location.href = `/list/redirect/schedule/${this.department}`;
        },
    },

}
</script>