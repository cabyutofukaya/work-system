<template>
    <Layout>

        <v-card flat tile>
            <v-card-text>
                <v-row>
                    <div class="w-25 ml-auto">
                        <v-btn @click.native="showDialog = true">年度表示</v-btn>
                    </div>

                </v-row>
            </v-card-text>
        </v-card>


        <table style="width: 100%;" class="mb-10">
            <thead>
                <tr>
                    <th class="text-left" style="width: 20%;">{{ month }}月[{{ category_data.name }}]</th>
                    <th class="text-left" v-for="(year, index) in select_year" :key="`${category}_header_${index}${year}`">
                        {{ year }}年
                    </th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>予算</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_budget_${index}${year}`">{{
                        sales_data[year].budget.toLocaleString() }}</td>
                </tr>


                <tr>
                    <td>売上</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_achievements_${index}${year}`">{{
                        sales_data[year].achievements.toLocaleString() }}</td>
                </tr>


                <tr>
                    <td>件数</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_count_${index}${year}`">{{
                        sales_data[year].count }}</td>
                </tr>

                <tr>
                    <td>各平均単価</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_tanka_${index}${year}`">{{
                        sales_data[year].tanka.toLocaleString() }}</td>
                </tr>


                <tr>
                    <td>利益</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_profit_${index}${year}`">{{
                        sales_data[year].profit.toLocaleString() }}</td>
                </tr>

                <tr>
                    <td>前年比（率)</td>
                    <td v-for="(year, index) in select_year" :key="`${category}_last_compare_${index}${year}`">{{
                        sales_data[year].last_month_compare_rate.toLocaleString() }}%</td>
                </tr>


            </tbody>
        </table>


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

        <template>
            <chart-bar :chart-data="chartBarData" :options="chartBarData.options" :styles="chartBarStyles" />
        </template>

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
}
</style>
  

<script>
// import SalesLayout from "./SalesLayout";
import { Link } from "@inertiajs/inertia-vue";
import Layout from "./Layout";

import ChartBar from '../Components/ChartBar'

export default {
    components: {
        Layout, ChartBar
    },

    props: ['type', 'type_list', 'category_list', 'sales_data', 'year_list', 'select_year', 'category', 'category_data', 'month', 'label_list'],


    data() {
        return {
            form: this.$inertia.form({
                type: this.type,
            }),
            form_year: this.$inertia.form({
                select: this.select_year,
            }),
            showDialog: false,
            loading: {},

            chartBarData: {},

            chartBarStyles: {
                height: '500px',
                position: 'relative'
            }
        }
    },

    mounted() {
        this.reload();
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
            this.reload();
        },

        reload: function () {

            var budget_list = [];
            var achievement_list = [];
            var rate_list = [];
            var year_string_list = [];

            var salesDataList = this.sales_data;

            this.select_year.forEach(function (year) {
                console.log(year);
                console.log(salesDataList[year].budget);
                console.log(salesDataList[year].achievements);

                budget_list.push(salesDataList[year].budget);
                achievement_list.push(salesDataList[year].achievements);
                rate_list.push(salesDataList[year].last_month_compare_rate);
                year_string_list.push(year + '年');
            });

            console.log(budget_list);
            console.log(achievement_list);

            this.chartBarData = {
                labels: year_string_list,
                datasets: [
                    {
                        label: '予算',
                        borderColor: '#e57373',
                        pointBackgroundColor: 'black',
                        borderWidth: 1,
                        pointBorderColor: 'black',
                        backgroundColor: 'rgb(229, 115, 115,0.3)',
                        data: budget_list,
                        yAxisID: "yAxis",
                    },
                    {
                        label: '売上',
                        borderColor: '#81c784',
                        pointBackgroundColor: 'black',
                        borderWidth: 1,
                        pointBorderColor: 'black',
                        backgroundColor: 'rgb(129, 199, 132,0.3)',
                        data: achievement_list,
                        yAxisID: "yAxis",
                    },
                    {
                        label: '前年比(率)',
                        borderColor: '#fab157',
                        pointBackgroundColor: '#8f5b13',
                        borderWidth: 1,
                        pointBorderColor: '#8f5b13',
                        backgroundColor: '#fab157',
                        data: rate_list,
                        fill: false,
                        datalabels: {
                            display: true,
                        },
                        type: 'line',
                        yAxisID: "yAxis-2",
                    }
                ],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        yAxes: [{
                            id: 'yAxis',
                            display: true,
                            ticks: {
                                beginAtZero: true
                            }
                        },
                        {
                            id: 'yAxis-2',
                            position: "right",
                            display: false,
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                }
            };
        },

    }
}
</script>
  