<style scoped>
.fc-day-sat {
    background-color: #eaf4ff;
}

.fc-day-sun {
    background-color: #ffeaea;
}

.calandar_event {
    background-color: white;
}
</style>

<template>
    <Layout>

        
        <v-card flat tile height="300">

            <FullCalendar class='demo-app-calendar' :options='calendarOptions' ref="fullCalendar">

            </FullCalendar>

        </v-card>


    

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
import 'tippy.js/dist/tippy.css';

export default {
    components: { Layout, Link, FullCalendar },

    props: ['memberData'],

    data() {

        var calendarOptionsList = [];

        return {
            showDialog: false,

            calendarOptions:{
                plugins: [timeGridPlugin, interactionPlugin],
                initialView: 'timeGridWeek',
                locales: [jaLocale],
                locale: "ja",
                businessHours: "true",
                height: "500",
                firstDay: "1",
                weekends: "true",
                // events: this.schedule,
                headerToolbar: {
                    // title, prev, next, prevYear, nextYear, today
                    left: '',
                    center: 'prev,next today',
                    right: '',
                    // center: 'title',
                    // right: 'timeGridWeek'
                },
                slotMinTime:"07:00:00",
                slotMaxTime:"20:00:00",
                defaultTimedEventDuration:"08:00:00",
                weekends:"true"
            
            },
            

            
            loading: {},
        }
    },

    

    methods: {

    },

}
</script>
  