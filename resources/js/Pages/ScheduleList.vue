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

        
        <v-card flat tile>

            <FullCalendar :options='calendarOptions'>

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

// import FullCalendar from '@fullcalendar/vue3'
// import resourceTimelinePlugin from '@fullcalendar/resource-timeline'

import 'tippy.js/dist/tippy.css';

export default {
    components: { Layout, Link, FullCalendar },

    props: ['memberData'],

    data() {

        return {
            showDialog: false,

            calendarOptions:{
                // plugins: [resourceTimelinePlugin],
            },
            

            
            loading: {},
        }
    },

    

    methods: {

    },

}
</script>
  