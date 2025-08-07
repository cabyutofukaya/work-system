<template>
    <v-card>
        <v-card-text class="px-10 py-6">
            <v-row>
                <v-col cols="12" class="text-right">
                    <v-icon @click="$emit('close')">mdi-close</v-icon>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="8">
                    <v-text-field type="date" v-model="localEvent.date" filled dense label="日付" />
                </v-col>
                <v-col cols="4">
                    <v-checkbox v-model="localEvent.allDay" label="終日" />
                </v-col>
            </v-row>

            <template v-if="!localEvent.allDay">
                <v-row>
                    <!-- 開始時刻 -->
                    <v-col cols="6">
                        <v-text-field v-model="localEvent.start_time" label="開始時刻" type="time" />
                    </v-col>

                    <!-- 終了時刻 -->
                    <v-col cols="6">
                        <v-text-field v-model="localEvent.end_time" label="終了時刻" type="time" />
                    </v-col>
                </v-row>
            </template>

            <v-row>
                <v-col cols="12">
                    <v-select v-model="localEvent.vehicle" :items="vehicleOptions" label="車両を選択" item-value="id"
                        item-text="name" filled dense />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="localEvent.number" filled dense label="予約番号を記載" />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-select v-model="localEvent.type" :items="descriptionOptions" label="用途を選択" filled dense />

                </v-col>
            </v-row>

            <v-row>
                <v-col class="text-right">
                    <v-btn :small="$vuetify.breakpoint.xs" color="primary" type="submit"
                        :loading="loading[isEditMode ? 'update' : 'create']" @click="emitSubmit">
                        <v-icon left>mdi-content-save-outline</v-icon>
                        <span>{{ isEditMode ? '更新' : '保存' }}</span>
                    </v-btn>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    name: 'EventForm',
    props: {
        event: { type: Object, required: true },
        vehicleOptions: { type: Array, required: true },
        isEditMode: { type: Boolean, required: true },
        loading: { type: Object, required: true }
    },
    data() {

        console.log(this.vehicleOptions);

        return {
            localEvent: {
                ...this.event,
            },
            descriptionOptions: ['直行', '時間貸し'],
        };
    },
    computed: {
        // startTime() {
        //     return `${this.localEvent.startHour}:${this.localEvent.startMinute}`;
        // },
        // endTime() {
        //     return `${this.localEvent.endHour}:${this.localEvent.endMinute}`;
        // }
    },
    watch: {
        event: {
            handler(newVal) {
                // const defaultStart = newVal.startTime?.split(':') || ['7', '00'];
                // const defaultEnd = newVal.endTime?.split(':') || ['9', '00'];

                this.localEvent = {
                    ...newVal,
                    // startHour: defaultStart[0],
                    // startMinute: defaultStart[1],
                    // endHour: defaultEnd[0],
                    // endMinute: defaultEnd[1]
                };
            },
            deep: true,
            immediate: true,
        },

    },
    methods: {
        emitSubmit() {
            // const startHour = parseInt(this.localEvent.startHour);
            // const startMinute = parseInt(this.localEvent.startMinute);
            // const endHour = parseInt(this.localEvent.endHour);
            // const endMinute = parseInt(this.localEvent.endMinute);


            // 終日の場合はバリデーションスキップ
            if (!this.localEvent.allDay) {
                // const startTotal = startHour * 60 + startMinute;
                // const endTotal = endHour * 60 + endMinute;

                // if (!this.localEvent.startHour || !this.localEvent.startMinute || !this.localEvent.endHour || !this.localEvent.endMinute) {
                //     alert('終日ではない場合は、「時間」の入力は必須です');
                //     return;
                // }


                // if (endTotal <= startTotal) {
                //     alert('終了時間は開始時間より後に設定してください');
                //     return;
                // }

                if (!this.localEvent.start_time || !this.localEvent.end_time) {
                    alert('終日ではない場合は、「時間」の入力は必須です');
                    return;
                }

                if (this.localEvent.end_time <= this.localEvent.start_time) {
                    alert('終了時間は開始時間より後に設定してください');
                    return;
                }
            }


            if (!this.localEvent.number) {
                alert('「予約番号」は必須です');
                return;
            }

            if (!this.localEvent.type) {
                alert('「用途」は必須です');
                return;
            }


            const payload = {
                ...this.localEvent,
                // startTime: this.startTime,
                // endTime: this.endTime
            };
            this.$emit('submit', payload);
        },

        // onAllDayChange(newVal) {
        //     if (!newVal) {
        //         this.localEvent.startHour = '7';
        //         this.localEvent.startMinute = '00';
        //         this.localEvent.endHour = '9';
        //         this.localEvent.endMinute = '00';
        //     }
        // }
    }
};
</script>
