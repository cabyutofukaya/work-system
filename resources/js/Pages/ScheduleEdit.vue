<template>
    <Layout>
        <v-card flat tile>


            <form @submit.prevent="update">
                <v-card-text>
                    <div class="description-form">

                        <v-hidden type="hidden" v-model="form.id"></v-hidden>

                        <v-row>
                            <v-col cols="12" sm="2">登録者</v-col>
                            <v-col>
                                <v-text-field type="name" name="user_name" v-model="form.user_name" disable></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12" sm="2">日付</v-col>
                            <v-col>
                                <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="form.date"
                                    :error="Boolean(form.errors.date)" :error-messages="form.errors.date"></v-text-field>
                            </v-col>
                        </v-row>


                        <v-row>
                            <v-col cols="12" sm="2">時間<br><v-checkbox name="enabled" v-model="form.enabled" label="終日"
                                    pt="0"></v-checkbox></v-col>

                            <v-col>
                                <v-text-field type="time" name="start_time" v-model="form.start_time"
                                    :disabled="form.enabled" label="開始時間"></v-text-field>
                            </v-col>

                            <v-col>
                                <v-text-field type="time" name="end_time" v-model="form.end_time" :disabled="form.enabled"
                                    label="終了時間"></v-text-field>
                            </v-col>
                        </v-row>


                        <v-row>
                            <v-col cols="12" sm="2">タイトル</v-col>
                            <v-col>
                                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="title" v-model="form.title"
                                    maxlength="200" :error="Boolean(form.errors.title)"
                                    :error-messages="form.errors.title"></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12" sm="2">内容</v-col>
                            <v-col>
                                <v-textarea dense filled prepend-inner-icon="mdi-pencil" name="content"
                                    v-model="form.content" maxlength="200" counter="200"
                                    :error="Boolean(form.errors.content)"
                                    :error-messages="form.errors.content"></v-textarea>
                            </v-col>
                        </v-row>



                    </div>

                    <v-row>
                        <v-col class="text-right">
                            <Button :small="$vuetify.breakpoint.xs" color="primary" type="submit"
                                :loading="loading['update']">
                                <v-icon left>
                                    mdi-content-save-outline
                                </v-icon>
                                内容を更新する
                            </Button>
                        </v-col>
                    </v-row>
                </v-card-text>
            </form>

            <v-divider class="mx-4"></v-divider>
            <v-card-text class="text-right">
                <BackButton></BackButton>
            </v-card-text>

        </v-card>

    </Layout>
</template>
  
<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";


export default {
    components: { Layout, Link },

    props: ['schedule', 'user'],

    data() {
        return {

            form: this.$inertia.form({
                id: Number(this.schedule.id) ?? 0,
                user_id: Number(this.user.id) ?? 0,
                date: this.schedule.date,
                start_time: this.schedule.start_time ? this.schedule.start_time : null,
                end_time: this.schedule.end_time ? this.schedule.end_time : null,
                title: this.schedule.title,
                content: this.schedule.content,
                user_name: this.user.name,
                enabled: this.schedule.start_time ? false : true,
            }),
            loading: {},
        }
    },

    methods: {

        update: function () {
            this.form.put(this.$route('schedule.update', { schedule: this.schedule.id }), {
                preserveState: (page) => Object.keys(page.props.errors).length,
                onStart: () => this.$set(this.loading, "update", true),
                onSuccess: () => this.$toasted.show('スケジュールの作成を更新しました'),
                onFinish: () => this.$set(this.loading, "update", false),
            }
            )
        },
    },


}
</script>
  