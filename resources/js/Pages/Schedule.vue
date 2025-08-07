<template>
    <Layout>
        <v-container>
            <h3 class="mb-4">カレンダー</h3>

            <v-row>
                <v-col>
                    <v-sheet height="650">
                        <v-toolbar flat color="white">
                            <v-btn icon @click="prev"><v-icon>mdi-chevron-left</v-icon></v-btn>
                            <v-btn icon @click="next"><v-icon>mdi-chevron-right</v-icon></v-btn>
                            <v-spacer />
                            <v-toolbar-title>{{ title }}</v-toolbar-title>
                            <v-spacer />
                        </v-toolbar>

                        <v-calendar ref="calendar" v-model="selectedDate" type="month" :events="events" color="primary"
                            :event-draggable="true" :event-resizable="false" @click:date="onDateClick"
                            @click:event="onEventClick" @change="onCalendarChange" />
                    </v-sheet>
                </v-col>
            </v-row>
        </v-container>

        <!-- モーダル（新規登録・編集） -->
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title class="headline">
                    {{ isEditMode ? 'スケジュール編集' : 'スケジュール登録' }}
                </v-card-title>
                <v-card-text>
                    <v-text-field v-model="editingEvent.title" label="タイトル" />
                    <v-textarea v-model="editingEvent.content" label="詳細" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn text @click="dialog = false">キャンセル</v-btn>
                    <v-btn v-if="isEditMode" color="error" text @click="deleteSchedule(editingEvent.id)">
                        削除
                    </v-btn>
                    <v-btn color="primary" @click="saveEvent">保存</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
import axios from 'axios';
import Layout from './Layout.vue';

export default {
    components: { Layout },
    data() {
        return {
            selectedDate: new Date().toISOString().substr(0, 10),
            dialog: false,
            isEditMode: false,
            events: [],
            editingEvent: {
                id: null,
                title: '',
                content: '',
                date: '',
            },
            titleText: '',
        };
    },
    computed: {
        title() {
            return this.titleText;
        },
    },
    methods: {
        // イベント読み込み
        async loadEvents(month = null) {
            const params = month ? { month } : {};
            const res = await axios.get('/api/schedules', { params });
            this.events = res.data.map(e => ({
                id: e.id,
                name: e.name,
                content: e.content,
                start: e.start, // YYYY-MM-DD
                end: e.end,
                color: e.color || '#2196f3',
            }));
        },

        // スケジュールが更新されたらタイトル取得＋ドラッグ更新
        onCalendarChange({ event }) {
            if (event?.dragged || event?.moved) {
                const updated = {
                    date: event.start.slice(0, 10),
                    title: event.name,
                    content: event.content || '',
                    all_day: true,
                };
                axios.put(`/api/schedules/${event.id}`, updated);
            }

            const cal = this.$refs.calendar;
            if (cal) this.titleText = cal.title;
        },

        // 月送り
        prev() {
            this.$refs.calendar.prev();
        },
        next() {
            this.$refs.calendar.next();
        },

        // 日付クリック → 新規登録モーダル表示
        onDateClick({ date }) {
            this.editingEvent = {
                id: null,
                title: '',
                content: '',
                date: date,
            };
            console.log(this.editingEvent);
            this.isEditMode = false;
            this.dialog = true;
        },

        // イベントクリック → 編集モーダル表示
        onEventClick({ event }) {
            this.editingEvent = {
                id: event.id,
                title: event.name,
                content: event.content || '',
                date: event.start.slice(0, 10),
            };
            this.isEditMode = true;
            this.dialog = true;
        },

        // 保存（新規 or 更新）
        async saveEvent() {
            const payload = {
                date: this.editingEvent.date,
                title: this.editingEvent.title || '新規スケジュール',
                content: this.editingEvent.content || '',
                all_day: true,
            };


            if (this.isEditMode && this.editingEvent.id) {
                // 更新処理
                const res = await axios.put(`/api/schedules/${this.editingEvent.id}`, payload);
                const updated = res.data.schedule;
                const index = this.events.findIndex(e => e.id === updated.id);
                if (index !== -1) {
                    this.events[index] = {
                        id: updated.id,
                        name: updated.title,
                        content: updated.content,
                        start: updated.date,
                        end: updated.date,
                        color: '#2196f3',
                    };
                }

                this.$toasted?.show('スケジュールを更新しました', { type: 'success' });

            } else {
                // 新規登録処理
                const res = await axios.post('/api/schedules', payload);
                const created = res.data.schedule;
                this.events.push({
                    id: created.id,
                    name: created.title,
                    content: created.content,
                    start: created.date,
                    end: created.date,
                    color: '#2196f3',
                });

                this.$toasted?.show('スケジュールを登録しました', { type: 'success' });
            }

            this.loadEvents();
            this.dialog = false;
        },

        // 削除
        async deleteSchedule(id) {
            await axios.delete(`/api/schedules/${id}`);
            this.events = this.events.filter(e => e.id !== id);
            this.dialog = false;
            this.$toasted?.show('スケジュールを削除しました', { type: 'success' });
        },
    },
    mounted() {
        this.loadEvents();
    },
};
</script>
