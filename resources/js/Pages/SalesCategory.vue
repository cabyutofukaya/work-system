<template>
    <Layout>

        <v-card flat tile>
            <v-card-text>
                <v-row>
                    <div class="w-25 ml-auto">
                        <v-select :items="year_list" dense filled v-model="year" @change="changeYear"
                            class="w-auto"></v-select>
                    </div>

                </v-row>
            </v-card-text>
        </v-card>


        <div class="font-weight-bold">{{ category.display_name }} [{{ type.name }}]</div>

        <div style="overflow-x: auto;">


            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 10%;">{{ year }}年</th>

                        <!-- <th class="text-left" v-for="(month, index) in month_sort_list"
                            :key="`header_${index}`" @click="clickMonth(month)" style="width: 5%;text-decoration:underline;">{{ month }}
                    </th> -->


                      

                       
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/1`" target="_blank">1月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/2`" target="_blank">2月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/3`" target="_blank">3月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/4`" target="_blank">4月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/5`" target="_blank">5月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/6`" target="_blank">6月</a></th>

                        <!-- <th class="text-left kamihanki" style="width: 5%;">下半期</th> -->

                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/7`" target="_blank">7月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/8`" target="_blank">8月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/9`" target="_blank">9月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/10`" target="_blank">10月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/11`" target="_blank">11月</a></th>
                        <th class="text-left" style="width: 5%;text-decoration:underline;"><a
                                :href="`/sales/billing/${category.name}/12`" target="_blank">12月</a></th>
 <!-- <th class="text-left shimohanki" style="width: 5%;">上半期</th> -->

                        <th class="text-left nenkei" style="width: 5%;">合計</th>
                    </tr>
                </thead>
                <tbody>

                    <tr class="budget">
                        <td>予算</td>

                        <td>{{ data[1].budget.toLocaleString() }}</td>
                        <td>{{ data[2].budget.toLocaleString() }}</td>
                        <td>{{ data[3].budget.toLocaleString() }}</td>
                        <td>{{ data[4].budget.toLocaleString() }}</td>
                        <td>{{ data[5].budget.toLocaleString() }}</td>
                        <td>{{ data[6].budget.toLocaleString() }}</td>

                        <td>{{ data[7].budget.toLocaleString() }}</td>
                        <td>{{ data[8].budget.toLocaleString() }}</td>
                        <td>{{ data[9].budget.toLocaleString() }}</td>
                        <td>{{ data[10].budget.toLocaleString() }}</td>
                        <td>{{ data[11].budget.toLocaleString() }}</td>
                        <td>{{ data[12].budget.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].budget.toLocaleString() }}円</td> -->

                       

                        <!-- <td class="shimohanki">{{ data[102].budget.toLocaleString() }}円</td> -->
                        <td class="nenkei">{{ data[200].budget.toLocaleString() }}円</td>
                    </tr>

                    <tr class="achievements">
                        <td>売上</td>

                        <td>{{ data[1].achievements.toLocaleString() }}</td>
                        <td>{{ data[2].achievements.toLocaleString() }}</td>
                        <td>{{ data[3].achievements.toLocaleString() }}</td>
                        <td>{{ data[4].achievements.toLocaleString() }}</td>
                        <td>{{ data[5].achievements.toLocaleString() }}</td>
                        <td>{{ data[6].achievements.toLocaleString() }}</td>

                        <td>{{ data[7].achievements.toLocaleString() }}</td>
                        <td>{{ data[8].achievements.toLocaleString() }}</td>
                        <td>{{ data[9].achievements.toLocaleString() }}</td>
                        <td>{{ data[10].achievements.toLocaleString() }}</td>
                        <td>{{ data[11].achievements.toLocaleString() }}</td>
                        <td>{{ data[12].achievements.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].achievements.toLocaleString() }}円</td> -->


                        

                        <!-- <td class="shimohanki">{{ data[102].achievements.toLocaleString() }}円</td> -->
                        <td class="nenkei">{{ data[200].achievements.toLocaleString() }}円</td>
                    </tr>



                    <tr>
                        <td>件数</td>

                        <td>{{ data[1].count }}件</td>
                        <td>{{ data[2].count }}件</td>
                        <td>{{ data[3].count }}件</td>
                        <td>{{ data[4].count }}件</td>
                        <td>{{ data[5].count }}件</td>
                        <td>{{ data[6].count }}件</td>

                        <td>{{ data[7].count }}件</td>
                        <td>{{ data[8].count }}件</td>
                        <td>{{ data[9].count }}件</td>
                        <td>{{ data[10].count }}件</td>
                        <td>{{ data[11].count }}件</td>
                        <td>{{ data[12].count }}件</td>

                        <!-- <td class="kamihanki">{{ data[101].count }}件</td> -->


                      

                        <!-- <td class="shimohanki">{{ data[102].count }}件</td> -->
                        <td class="nenkei">{{ data[200].count }}件</td>

                    </tr>

                    <tr>
                        <td>平均単価</td>

                        <td>{{ data[1].average_price.toLocaleString() }}</td>
                        <td>{{ data[2].average_price.toLocaleString() }}</td>
                        <td>{{ data[3].average_price.toLocaleString() }}</td>
                        <td>{{ data[4].average_price.toLocaleString() }}</td>
                        <td>{{ data[5].average_price.toLocaleString() }}</td>
                        <td>{{ data[6].average_price.toLocaleString() }}</td>

                        <td>{{ data[7].average_price.toLocaleString() }}</td>
                        <td>{{ data[8].average_price.toLocaleString() }}</td>
                        <td>{{ data[9].average_price.toLocaleString() }}</td>
                        <td>{{ data[10].average_price.toLocaleString() }}</td>
                        <td>{{ data[11].average_price.toLocaleString() }}</td>
                        <td>{{ data[12].average_price.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].average_price.toLocaleString() }}円</td> -->

                    

                        <!-- <td class="shimohanki">{{ data[102].average_price.toLocaleString() }}円</td> -->

                        <td class="nenkei">{{ data[200].average_price.toLocaleString() }}円</td>

                    </tr>


                    <tr>
                        <td>利益</td>

                        <td>{{ data[1].profit.toLocaleString() }}</td>
                        <td>{{ data[2].profit.toLocaleString() }}</td>
                        <td>{{ data[3].profit.toLocaleString() }}</td>
                        <td>{{ data[4].profit.toLocaleString() }}</td>
                        <td>{{ data[5].profit.toLocaleString() }}</td>
                        <td>{{ data[6].profit.toLocaleString() }}</td>

                        <td>{{ data[7].profit.toLocaleString() }}</td>
                        <td>{{ data[8].profit.toLocaleString() }}</td>
                        <td>{{ data[9].profit.toLocaleString() }}</td>
                        <td>{{ data[10].profit.toLocaleString() }}</td>
                        <td>{{ data[11].profit.toLocaleString() }}</td>
                        <td>{{ data[12].profit.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].profit.toLocaleString() }}円</td> -->

                       

                        <!-- <td class="shimohanki">{{ data[102].profit.toLocaleString() }}円</td> -->
                        <td class="nenkei">{{ data[200].profit.toLocaleString() }}円</td>

                    </tr>

                    <tr class="last_year_achievements">
                        <td>前年売上</td>

                        <td>{{ data[1].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[2].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[3].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[4].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[5].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[6].last_year_achievements.toLocaleString() }}</td>

                        <td>{{ data[7].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[8].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[9].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[10].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[11].last_year_achievements.toLocaleString() }}</td>
                        <td>{{ data[12].last_year_achievements.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].last_year_achievements.toLocaleString() }}円</td> -->

                      
                        <!-- <td class="shimohanki">{{ data[102].last_year_achievements.toLocaleString() }}円</td> -->

                        <td class="nenkei">{{ data[200].last_year_achievements.toLocaleString() }}円</td>
                    </tr>

                    <tr>
                        <td>予算比(額)</td>

                        <td>{{ data[1].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[2].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[3].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[4].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[5].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[6].budget_compare_price.toLocaleString() }}</td>

                        <td>{{ data[7].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[8].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[9].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[10].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[11].budget_compare_price.toLocaleString() }}</td>
                        <td>{{ data[12].budget_compare_price.toLocaleString() }}</td>


                        <!-- <td class="kamihanki">{{ data[101].budget_compare_price.toLocaleString() }}円</td> -->

                     

                        <!-- <td class="shimohanki">{{ data[102].budget_compare_price.toLocaleString() }}円</td> -->
                        <td class="nenkei">{{ data[200].budget_compare_price.toLocaleString() }}円</td>
                    </tr>

                    <tr>
                        <td>前年比（額)</td>

                        <td>{{ data[1].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[2].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[3].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[4].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[5].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[6].last_month_compare_price.toLocaleString() }}</td>

                        <td>{{ data[7].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[8].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[9].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[10].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[11].last_month_compare_price.toLocaleString() }}</td>
                        <td>{{ data[12].last_month_compare_price.toLocaleString() }}</td>

                        <!-- <td class="kamihanki">{{ data[101].last_month_compare_price.toLocaleString() }}円</td> -->

                       

                        <!-- <td class="shimohanki">{{ data[102].last_month_compare_price.toLocaleString() }}円</td> -->
                        <td class="nenkei">{{ data[200].last_month_compare_price.toLocaleString() }}円</td>
                    </tr>


                    <tr>
                        <td>予算比（率)</td>

                        <td>{{ data[1].budget_compare_rate }}%</td>
                        <td>{{ data[2].budget_compare_rate }}%</td>
                        <td>{{ data[3].budget_compare_rate }}%</td>
                        <td>{{ data[4].budget_compare_rate }}%</td>
                        <td>{{ data[5].budget_compare_rate }}%</td>
                        <td>{{ data[6].budget_compare_rate }}%</td>

                        <td>{{ data[7].budget_compare_rate }}%</td>
                        <td>{{ data[8].budget_compare_rate }}%</td>
                        <td>{{ data[9].budget_compare_rate }}%</td>
                        <td>{{ data[10].budget_compare_rate }}%</td>
                        <td>{{ data[11].budget_compare_rate }}%</td>
                        <td>{{ data[12].budget_compare_rate }}%</td>

                        <!-- <td class="kamihanki">{{ data[101].budget_compare_rate }}%</td> -->

                       
                        <!-- <td class="shimohanki">{{ data[102].budget_compare_rate }}%</td> -->

                        <td class="nenkei">{{ data[200].budget_compare_rate }}%</td>
                    </tr>


                    <tr>
                        <td>前年比（率)</td>

                        <td>{{ data[1].last_month_compare_rate }}%</td>
                        <td>{{ data[2].last_month_compare_rate }}%</td>
                        <td>{{ data[3].last_month_compare_rate }}%</td>
                        <td>{{ data[4].last_month_compare_rate }}%</td>
                        <td>{{ data[5].last_month_compare_rate }}%</td>
                        <td>{{ data[6].last_month_compare_rate }}%</td>

                        <td>{{ data[7].last_month_compare_rate }}%</td>
                        <td>{{ data[8].last_month_compare_rate }}%</td>
                        <td>{{ data[9].last_month_compare_rate }}%</td>
                        <td>{{ data[10].last_month_compare_rate }}%</td>
                        <td>{{ data[11].last_month_compare_rate }}%</td>
                        <td>{{ data[12].last_month_compare_rate }}%</td>


                        <!-- <td class="kamihanki">{{ data[101].last_month_compare_rate }}%</td> -->
                      
                        <!-- <td class="shimohanki">{{ data[102].last_month_compare_rate }}%</td> -->
                        <td class="nenkei">{{ data[200].last_month_compare_rate }}%</td>
                    </tr>


                    <tr v-if="this.user.admin_sales == 1">
                        <td style="border-bottom: none;"></td>

                        <td style="border-bottom: none;"><v-btn @click="clickMonth(1)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(2)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(3)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(4)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(5)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(6)">編集</v-btn></td>

                        <td style="border-bottom: none;"><v-btn @click="clickMonth(7)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(8)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(9)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(10)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(11)">編集</v-btn></td>
                        <td style="border-bottom: none;"><v-btn @click="clickMonth(12)">編集</v-btn></td>

                        <!-- <td style="border-bottom: none;"></td> -->

                       

                        <!-- <td style="border-bottom: none;"></td> -->
                        <td style="border-bottom: none;"></td>
                    </tr>


                </tbody>
            </table>
        </div>



        <!-- お知らせ追加ダイアログ -->
        <v-dialog v-model="showDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
            @click:outside="showDialog = false">
            <v-card flat tile>
                <v-card-title>
                    変更画面
                </v-card-title>

                <v-card-text>

                    <v-text-field dense filled prepend-inner-icon="mdi-calendar-month" label="月" readonly
                        v-model="form.month"></v-text-field>

                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="予算" name="budget"
                        v-model="form.budget" :error="Boolean(form.errors.budget)" :error-messages="form.errors.budget"
                        type="number"></v-text-field>

                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="売上" name="achievements"
                        v-model="form.achievements" :error="Boolean(form.errors.achievements)"
                        :error-messages="form.errors.achievements" type="number"></v-text-field>

                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="件数" name="count" v-model="form.count"
                        :error="Boolean(form.errors.count)" :error-messages="form.errors.count"
                        type="number"></v-text-field>


                    <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="利益" name="profit" v-model="form.profit"
                        :error="Boolean(form.errors.profit)" :error-messages="form.errors.profit"
                        type="number"></v-text-field>

                </v-card-text>

                <v-card-text class="text-center">
                    <Button color="primary" :small="$vuetify.breakpoint.xs" @click.native="update"
                        :loading="loading['update']">
                        <v-icon left>
                            mdi-content-save-edit-outline
                        </v-icon>
                        この内容で変更する
                    </Button>
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

        <div class="mt-10">
            <template>
                <chart-bar :chart-data="chartBarData" :options="chartBarData.options" :styles="chartBarStyles" />
            </template>
        </div>



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
    background: rgb(184, 236, 242);
    padding: 5px;
}

td {
    border-top: 1px solid #333;
    border-bottom: 1px solid #333;
    padding: 5px;
    white-space: nowrap;
}

.last_year_achievements {
    background: rgb(181, 195, 236);
}

.achievements {
    background: rgb(255, 217, 156);
}

.budget {
    background: rgb(250, 248, 182);
}

.kamihanki {
    background: rgb(166, 166, 165);
}

.shimohanki {
    background: rgb(166, 166, 165);
}

.nenkei {
    background: rgb(186, 225, 226);
}
</style>
  
  
  
  
  
<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

import ChartBar from '../Components/ChartBar'

export default {
    components: { Link, Layout, ChartBar },

    props: ['data', 'year', 'category', 'type', 'year_list', 'month_sort_list','user'],


    data() {
        return {
            form: this.$inertia.form({
                id: undefined,
                achievements: undefined,
                budget: undefined,
                count: undefined,
                year: this.year,
                month: undefined,
                category: this.category.id,
                profit:undefined,
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
        //月の情報取得
        clickMonth: function (month) {

            axios
                .get(`/sales/${this.category.name}/${this.year}/${month}`)
                .then((res) => {
                    var tmpData = res.data;
                    console.log('データ取得' + res.data);

                    console.log(tmpData['id']);
                    console.log(tmpData['achievements']);
                    console.log(tmpData['budget']);
                    console.log(tmpData['count']);

                    this.form.id = tmpData['id'];
                    this.form.achievements = tmpData['achievements'];
                    this.form.budget = tmpData['budget'];
                    this.form.count = tmpData['count'];
                    this.form.profit = tmpData['profit'];
                    this.form.month = month + '月';

                    this.showDialog = true;
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {

                });

        },
        update: function () {
            this.form.post(this.$route('sales.update'), {
                onStart: () => this.$set(this.loading, "update", true),
                onSuccess: () => this.$toasted.show('請求管理情報を更新しました'),
                onFinish: () => this.$set(this.loading, "update", false),
            })
        },

        changeYear: function (year) {
            this.$inertia.get(`/sales/billing/${this.category.name}/${year}`);
        },

        reload: function () {

            var budget_list = [];
            var achievement_list = [];
            var rate_list = [];
            var month_string_list = [];

            var salesDataList = this.data;

            var month_list = [ '1', '2', '3', '4', '5', '6','7', '8', '9', '10', '11', '12'];

            month_list.forEach(function (month) {
                console.log(month);
                console.log(salesDataList[month].budget);
                console.log(salesDataList[month].achievements);

                budget_list.push(salesDataList[month].budget);
                achievement_list.push(salesDataList[month].achievements);
                rate_list.push(salesDataList[month].last_month_compare_rate);
                month_string_list.push(month + '月');
            });

            console.log(budget_list);
            console.log(achievement_list);

            this.chartBarData = {
                labels: month_string_list,
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
    },

}
</script>
  