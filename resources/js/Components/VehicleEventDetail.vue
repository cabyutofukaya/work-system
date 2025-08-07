<template>
    <v-card>
        <v-card-text class="px-10 py-6">
            <v-row>
                <v-col cols="12" class="text-right">
                    <template v-if="user.id === event?.user?.id">
                        <v-icon @click="$emit('edit')">mdi-pencil</v-icon>
                        <v-icon @click="$emit('delete')" class="ml-4">mdi-delete-outline</v-icon>
                    </template>
                    <v-icon @click="$emit('close')" class="ml-8">mdi-close</v-icon>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="1"><v-icon>mdi-calendar-clock</v-icon></v-col>
                <v-col cols="11">
                    {{ formatDateToJapanese(event?.start) }}
                    <template v-if="!event.allDay">
                        ( {{ event.start_time }} ~ {{ event.end_time }} )
                    </template>
                    <template v-else>
                        - 終日
                    </template>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="1"><v-icon>mdi-car</v-icon></v-col>
                <v-col cols="11">{{ event?.vehicle_name }}</v-col>
            </v-row>

            <v-row>
                <v-col cols="1"><v-icon>mdi-circle-medium</v-icon></v-col>
                <v-col cols="11">{{ event?.number }}</v-col>
            </v-row>

            <v-row>
                <v-col cols="1"><v-icon>mdi-format-float-left</v-icon></v-col>
                <v-col cols="11">{{ event?.type }}</v-col>
            </v-row>

            <v-row>
                <v-col cols="1"><v-icon>mdi-account</v-icon></v-col>
                <v-col cols="11">{{ event?.user?.name }}</v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    name: 'EventDetail',
    props: {
        event: { type: Object, required: true },
        vehicle: { type: Array, required: true },
        user: { type: Object, required: true }
    },
    methods: {
        formatDateToJapanese(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('ja-JP', { month: 'numeric', day: 'numeric' });
        }
    }
};
</script>
