<template>
    <v-container fluid class="pa-0">
        <FullCalendar :options="calendarOptions" ref="calendar" style="min-width: 900px; max-width: 1400px;" />
    </v-container>
</template>

<script>
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import jaLocale from '@fullcalendar/core/locales/ja';

export default {
    name: 'ScheduleCalendarView',
    components: { FullCalendar },
    props: {
        events: { type: Array, required: true },
        user: { type: Object, required: true },
    },
    data() {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                initialDate: new Date(),
                locales: [jaLocale],
                locale: 'ja',
                businessHours: {
                    startTime: '06:00',
                    endTime: '20:00',
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6]
                },
                firstDay: 1,
                weekends: true,
                height: 'auto',
                width: 'auto',
                allDaySlot: true,
                selectable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth timeGridWeek'
                },
                // events: this.events,
                events: this.filteredEvents,
                dateClick: this.onDateClick,
                eventClick: this.onEventClick,
                datesSet: this.onDatesSet,
                slotMinTime: "06:00:00",
                slotMaxTime: "21:00:00",
            },
            selectedVehicle: null,
        };
    },
    watch: {
        events: {
            handler() {
                this.updateEvents();
            },
            deep: true
        },
        selectedVehicle() {
            this.updateEvents();
        }
    },
    computed: {
        filteredEvents() {
            if (!this.selectedVehicle) return this.events;
            return this.events.filter(event => event?.vehicle_id == this.selectedVehicle);
        }
    },
    methods: {
        onDateClick(info) {
            this.$emit('open-edit', info.dateStr);
        },
        updateEvents() {
            this.calendarOptions.events = this.filteredEvents;
        },
        onEventClick(info) {
            console.log(info);
            this.$emit('open-view', {
                id: info.event.id,
                start: info.event.extendedProps.startDate,
                allDay: info.event.allDay,
                user: info.event.extendedProps.user,
                startTime: info.event.extendedProps.start_time,
                endTime: info.event.extendedProps.end_time,
            });
        },
        onDatesSet(info) {
            this.$emit('date-change', info);
        }
    }
};
</script>
