<template>
    <Layout>
        <v-container>
            <v-row>
                <v-col cols="4"> <v-select :items="userList" item-value="id" item-text="name" v-model="selectedUserId"
                        label="ユーザーを選択" outlined dense class="mt-4 mb-2" /></v-col>
            </v-row>


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

        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h6 font-weight-bold">
                    {{ isEditMode ? 'スケジュール編集' : 'スケジュール登録' }}
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pt-4">
                    <v-text-field v-model="editingEvent.title" label="タイトル" outlined dense class="mb-4" />
                    <v-select :items="categoryList" v-model="editingEvent.category" label="カテゴリ" outlined dense />
                    <v-textarea v-model="editingEvent.content" label="詳細" outlined dense rows="3" />
                </v-card-text>

                <template v-if="isEditMode && loginUser.id === editingEvent.user_id">
                    <v-card-actions class="justify-end px-4 pb-4">
                        <v-btn text @click="dialog = false">キャンセル</v-btn>
                        <v-btn color="error" text @click="deleteSchedule(editingEvent.id)">削除</v-btn>
                        <v-btn color="primary" @click="saveEvent">保存</v-btn>
                    </v-card-actions>
                </template>

                <!-- 新規登録モードの場合（誰でも登録可） -->
                <template v-else-if="!isEditMode">
                    <v-card-actions class="justify-end px-4 pb-4">
                        <v-btn text @click="dialog = false">キャンセル</v-btn>
                        <v-btn color="primary" @click="saveEvent">保存</v-btn>
                    </v-card-actions>
                </template>


            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
import axios from 'axios';
import Layout from './Layout.vue';
axios.defaults.withCredentials = true;

export default {
    components: { Layout },
    props: [
        'categoryList',
        'userList',
        'loginUser',
    ],
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
                category: '',
                user_id: this.loginUser.id,
            },
            selectedUserId: this.loginUser?.id || null,
            titleText: '',
        };
    },
    computed: {
        title() {
            return this.titleText;
        },
    },
    watch: {
        selectedUserId() {
            this.loadEvents();
        },
    },
    methods: {
        async loadEvents(month = null) {
            await axios.get('/sanctum/csrf-cookie');
            const params = {};
            if (month) params.month = month;
            if (this.selectedUserId) params.user_id = this.selectedUserId;
            const res = await axios.get('/api/calendar', { params });

            console.log(res.data);

            this.events = res.data.filter(e => !!e.start)
                .map(e => ({
                    id: e.id,
                    name: e.title,
                    content: e.content,
                    start: e.start,
                    end: e.start,
                    color: e.color,
                    category: e.category,
                    user_id: e.user_id,
                }));
        },
        async onCalendarChange({ start, event }) {
            await axios.get('/sanctum/csrf-cookie');
            if (event?.dragged || event?.moved) {
                const updated = {
                    date: event.start.slice(0, 10),
                    title: event.name,
                    content: event.content || '',
                    all_day: true,
                };
                axios.put(`/api/calendar/${event.id}`, updated);
            }
            const cal = this.$refs.calendar;
            if (cal) this.titleText = cal.title;
            if (start) this.loadEvents(start.slice(0, 7));
        },
        prev() {
            this.$refs.calendar.prev();
        },
        next() {
            this.$refs.calendar.next();
        },
        onDateClick({ date }) {
            this.editingEvent = {
                id: null,
                title: '',
                content: '',
                date,
                category: '',
                user_id: null,
            };
            this.isEditMode = false;
            this.dialog = true;
        },
        onEventClick({ event }) {
            this.editingEvent = {
                id: event.id,
                title: event.name,
                content: event.content || '',
                date: event.start.slice(0, 10),
                category: event.category || '',
                user_id: event.user_id,
            };
            this.isEditMode = true;
            this.dialog = true;
        },
        async saveEvent() {

            // タイトル必須チェック
            if (!this.editingEvent.title || this.editingEvent.title.trim() === '') {
                alert('タイトルは必須です');
                return;
            }

            await axios.get('/sanctum/csrf-cookie');
            const payload = {
                date: this.editingEvent.date,
                title: this.editingEvent.title,
                content: this.editingEvent.content || '',
                category: this.editingEvent.category || null,
                user_id: this.loginUser?.id,
            };


            try {
                if (this.isEditMode && this.editingEvent.id) {
                    await axios.put(`/api/calendar/${this.editingEvent.id}`, payload);
                    this.$toasted.show('スケジュールを更新しました', {
                        type: 'success',
                        duration: 2500,
                        position: 'bottom-right'
                    });
                } else {
                    await axios.post('/api/calendar', payload);
                    this.$toasted.show('スケジュールを登録しました', {
                        type: 'success',
                        duration: 2500,
                        position: 'bottom-right'
                    });
                }
            } catch (e) {
                console.error(e);
                this.$toasted.show('保存に失敗しました', {
                    type: 'error',
                    duration: 2500,
                    position: 'bottom-right'
                });
            }

            this.dialog = false;
            this.loadEvents();
        },
        async deleteSchedule(id) {
            await axios.get('/sanctum/csrf-cookie');
            await axios.delete(`/api/calendar/${id}`);

            this.$toasted.show('スケジュールを削除しました', {
                type: 'success',
                duration: 2500,
                position: 'bottom-right'
            });

            this.events = this.events.filter(e => e.id !== id);
            this.dialog = false;
        },
    },
    mounted() {
        this.loadEvents();
    },
};
</script>