<template>
    <Layout>

        <v-card flat tile>
            <v-card-text>
                <v-row>
                    <div class="w-25 mr-auto">
                        <v-select :items="type_list" dense filled v-model="form.type" @change="changeType"
                            class="w-auto"></v-select>
                    </div>

                    <div class="w-25 ml-auto">
                        <v-btn @click.native="showDialog = true">年表示</v-btn>
                    </div>

                </v-row>
            </v-card-text>
        </v-card>

    
        <div v-for="(sales, category) in sales_data" :key="sales.id">

            <div style="overflow-x: auto;">
            <table style="width: 100%;overflow-x: auto;" class="mb-10">
                <thead>


                    <tr>
                        <th class="text-left" style="width: 20%;">{{ category_list[category] }}</th>
                        <th class="text-left" v-for="(year, index) in select_year"
                            :key="`${category}_header_${index}${year}`"><a :href="`/sales/billing/${category}/${year}`"
                                target="_blank">{{ year }}年</a>
                        </th>
                    </tr>

                </thead>
                <tbody>

                    <tr>
                        <td>予算</td>
                        <td v-for="(year, index) in select_year" :key="`${category}_budget_${index}${year}`">{{
                            sales_data[category][year].budget.toLocaleString() }}円</td>
                    </tr>


                    <tr>
                        <td>売上</td>
                        <td v-for="(year, index) in select_year" :key="`${category}_achievements_${index}${year}`">{{
                            sales_data[category][year].achievements.toLocaleString() }}円</td>
                    </tr>

                    
                 
                    <tr>
                        <td>件数</td>
                        <td v-for="(year, index) in select_year" :key="`${category}_count_${index}${year}`">{{
                            sales_data[category][year].count }}件</td>
                    </tr>

                    <tr>
                        <td>各平均単価</td>
                        <td v-for="(year, index) in select_year" :key="`${category}_tanka_${index}${year}`">{{
                            sales_data[category][year].tanka.toLocaleString() }}円</td>
                    </tr>


                    <tr>
                        <td>利益</td>
                        <td v-for="(year, index) in select_year" :key="`${category}_profit_${index}${year}`">{{
                            sales_data[category][year].profit.toLocaleString() }}円</td>
                    </tr>


                </tbody>
            </table>
            </div>


        </div>


        <!-- お知らせ追加ダイアログ -->
        <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
            @click:outside="showDialog = false">
            <v-card flat tile>

                <v-card-title>
                    年表示
                </v-card-title>

                <v-card-text>

                    <v-select :items="year_list" dense filled multiple class="w-auto" v-model="select_year"
                        @change="changeSelectYear"></v-select>

                </v-card-text>

                <!-- <v-card-text class="text-center">
                    <Button color="primary" :small="$vuetify.breakpoint.xs" @click.native="update"
                        :loading="loading['update']">
                        <v-icon left>
                            mdi-content-save-edit-outline
                        </v-icon>
                        表示変更する
                    </Button>
                </v-card-text> -->

                <v-divider></v-divider>

                <v-card-text class="text-right">
                    <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="showDialog = false">
                        <v-icon>
                            mdi-close
                        </v-icon>
                        閉じる
                    </Button>
                </v-card-text>
            </v-card>
        </v-dialog>


    </Layout>
</template>
  
<style>
table {
    width: 90%;
    border-collapse: collapse;
}

th {
    border-top: 1px solid #333;
    border-bottom: 1px solid #333;
    background: rgb(181, 195, 236);
    padding: 5px;
}

td {
    border-top: 1px solid #333;
    border-bottom: 1px solid #333;
    background: #ffffff;
    padding: 5px;
    white-space: nowrap;
}
</style>
  
  
  
  
  
<script>
// import SalesLayout from "./SalesLayout";
import { Link } from "@inertiajs/inertia-vue";
import Layout from "./Layout";

export default {
    components: { Layout },

    props: ['type', 'type_list', 'category_list', 'sales_data', 'year_list', 'select_year'],


    data() {
        return {
            form: this.$inertia.form({
                type: this.type,
            }),
            form_year: this.$inertia.form({
                select: this.select_year,
            }),
            showDialog: false,
            loading: {}
        }
    },
    computed: {

    },

    methods: {
        changeType: function (type) {
            this.$inertia.get(`/sales/${type}`);
        },

        update: function () {
            alert(this.form_year.select);
            this.$inertia.get(`/sales/${this.type}?year=${this.form_year.select}`);
        },

        changeSelectYear: function () {
            this.select_year.sort();
        },
    }
}
</script>
  