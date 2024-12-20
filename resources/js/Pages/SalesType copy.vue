<template>
    <Layout>

        <v-card flat tile>
            <v-card-text>
                <v-row>

                    <v-select label="Select" :items="type_list" multiple dense filled clearable
                        v-model="form.type"></v-select>

                    <v-btn class="mt-1 ml-3 lime-lighten-2">
                        表示変更する
                    </v-btn>


                </v-row>
            </v-card-text>
        </v-card>

        <div v-if="type == 'wsd'">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">たびの足(タクシー)</th>

                        <th class="text-left" style="width: 20%;" v-if="select_year.includes('2015')">2015</th>

                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_taxi/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_taxi/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_taxi/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_taxi/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_taxi/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2019].count }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2020].count }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2021].count }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2022].count }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_taxi'][2023].tanka.toLocaleString() }}</td>
                    </tr>

                </tbody>
            </table>



            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">たびの足(バス)</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_bus/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_bus/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_bus/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_bus/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/tabinoashi_bus/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['tabinoashi_bus'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['tabinoashi_bus'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['tabinoashi_bus'][2019].count }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2020].count }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2021].count }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2022].count }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['tabinoashi_bus'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['tabinoashi_bus'][2023].tanka.toLocaleString() }}</td>
                    </tr>


                </tbody>
            </table>

            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">屋形船サイト</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/yakatafune/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/yakatafune/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/yakatafune/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/yakatafune/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/yakatafune/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['yakatafune'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['yakatafune'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['yakatafune'][2019].count }}</td>
                        <td>{{ sales_data['yakatafune'][2020].count }}</td>
                        <td>{{ sales_data['yakatafune'][2021].count }}</td>
                        <td>{{ sales_data['yakatafune'][2022].count }}</td>
                        <td>{{ sales_data['yakatafune'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['yakatafune'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['yakatafune'][2023].tanka.toLocaleString() }}</td>
                    </tr>

                </tbody>
            </table>

            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">貸切バスの匠</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/kashikiribus/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/kashikiribus/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/kashikiribus/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/kashikiribus/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/wsd/kashikiribus/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['kashikiribus'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['kashikiribus'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['kashikiribus'][2019].count }}</td>
                        <td>{{ sales_data['kashikiribus'][2020].count }}</td>
                        <td>{{ sales_data['kashikiribus'][2021].count }}</td>
                        <td>{{ sales_data['kashikiribus'][2022].count }}</td>
                        <td>{{ sales_data['kashikiribus'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['kashikiribus'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['kashikiribus'][2023].tanka.toLocaleString() }}</td>
                    </tr>




                </tbody>
            </table>

        </div>


        <div v-if="type == 'icts'">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">バスキング</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/bking/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/bking/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/bking/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/bking/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/bking/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['bking'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['bking'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['bking'][2019].count }}</td>
                        <td>{{ sales_data['bking'][2020].count }}</td>
                        <td>{{ sales_data['bking'][2021].count }}</td>
                        <td>{{ sales_data['bking'][2022].count }}</td>
                        <td>{{ sales_data['bking'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['bking'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bking'][2023].tanka.toLocaleString() }}</td>
                    </tr>




                </tbody>
            </table>



            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">グッドラーニング</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/goodlearning/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/goodlearning/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/goodlearning/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/goodlearning/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/goodlearning/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['goodlearning'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['goodlearning'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['goodlearning'][2019].count }}</td>
                        <td>{{ sales_data['goodlearning'][2020].count }}</td>
                        <td>{{ sales_data['goodlearning'][2021].count }}</td>
                        <td>{{ sales_data['goodlearning'][2022].count }}</td>
                        <td>{{ sales_data['goodlearning'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['goodlearning'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['goodlearning'][2023].tanka.toLocaleString() }}</td>
                    </tr>


                </tbody>
            </table>

            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">トラックキング</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/truckking/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/truckking/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/truckking/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/truckking/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/icts/truckking/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['truckking'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['truckking'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['truckking'][2019].count }}</td>
                        <td>{{ sales_data['truckking'][2020].count }}</td>
                        <td>{{ sales_data['truckking'][2021].count }}</td>
                        <td>{{ sales_data['truckking'][2022].count }}</td>
                        <td>{{ sales_data['truckking'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['truckking'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['truckking'][2023].tanka.toLocaleString() }}</td>
                    </tr>




                </tbody>
            </table>

        </div>


        <div v-if="type == 'bpd'">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">タクシー</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/taxi/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/taxi/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/taxi/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/taxi/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/taxi/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['taxi'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['taxi'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['taxi'][2019].count }}</td>
                        <td>{{ sales_data['taxi'][2020].count }}</td>
                        <td>{{ sales_data['taxi'][2021].count }}</td>
                        <td>{{ sales_data['taxi'][2022].count }}</td>
                        <td>{{ sales_data['taxi'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['taxi'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['taxi'][2023].tanka.toLocaleString() }}</td>
                    </tr>




                </tbody>
            </table>



            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">バス</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/bus/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/bus/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/bus/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/bus/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/bus/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['bus'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['bus'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['bus'][2019].count }}</td>
                        <td>{{ sales_data['bus'][2020].count }}</td>
                        <td>{{ sales_data['bus'][2021].count }}</td>
                        <td>{{ sales_data['bus'][2022].count }}</td>
                        <td>{{ sales_data['bus'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['bus'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['bus'][2023].tanka.toLocaleString() }}</td>
                    </tr>



                </tbody>
            </table>

            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">CBL</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/cbl/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/cbl/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/cbl/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/cbl/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/bpd/cbl/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['cbl'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['cbl'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['cbl'][2019].count }}</td>
                        <td>{{ sales_data['cbl'][2020].count }}</td>
                        <td>{{ sales_data['cbl'][2021].count }}</td>
                        <td>{{ sales_data['cbl'][2022].count }}</td>
                        <td>{{ sales_data['cbl'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['cbl'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['cbl'][2023].tanka.toLocaleString() }}</td>
                    </tr>



                </tbody>
            </table>

        </div>



        <div v-if="type == 'tcd'">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">Hakken</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/hakken/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/hakken/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/hakken/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/hakken/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/hakken/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['hakken'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['hakken'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['hakken'][2019].count }}</td>
                        <td>{{ sales_data['hakken'][2020].count }}</td>
                        <td>{{ sales_data['hakken'][2021].count }}</td>
                        <td>{{ sales_data['hakken'][2022].count }}</td>
                        <td>{{ sales_data['hakken'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['hakken'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['hakken'][2023].tanka.toLocaleString() }}</td>
                    </tr>



                </tbody>
            </table>



            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">Links</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/links/2019" target="_blank">2019</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/links/2020" target="_blank">2020</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/links/2021" target="_blank">2021</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/links/2022" target="_blank">2022</a>
                        </th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/links/2023" target="_blank">2023</a>
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['links'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['links'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['links'][2019].count }}</td>
                        <td>{{ sales_data['links'][2020].count }}</td>
                        <td>{{ sales_data['links'][2021].count }}</td>
                        <td>{{ sales_data['links'][2022].count }}</td>
                        <td>{{ sales_data['links'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['links'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['links'][2023].tanka.toLocaleString() }}</td>
                    </tr>


                </tbody>
            </table>

            <br />

            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 20%;">JTB-BOKUN</th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/jtbbokun/2019"
                                target="_blank">2019</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/jtbbokun/2020"
                                target="_blank">2020</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/jtbbokun/2021"
                                target="_blank">2021</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/jtbbokun/2022"
                                target="_blank">2022</a></th>
                        <th class="text-left" style="width: 17%;"><a href="/sales/tcd/jtbbokun/2023"
                                target="_blank">2023</a></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>売上</td>
                        <td>{{ sales_data['jtbbokun'][2019].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2020].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2021].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2022].budget.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2023].budget.toLocaleString() }}</td>
                    </tr>

                    <tr>
                        <td>利益</td>
                        <td>{{ sales_data['jtbbokun'][2019].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2020].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2021].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2022].achievements.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2023].achievements.toLocaleString() }}</td>
                    </tr>


                    <tr>
                        <td>件数</td>
                        <td>{{ sales_data['jtbbokun'][2019].count }}</td>
                        <td>{{ sales_data['jtbbokun'][2020].count }}</td>
                        <td>{{ sales_data['jtbbokun'][2021].count }}</td>
                        <td>{{ sales_data['jtbbokun'][2022].count }}</td>
                        <td>{{ sales_data['jtbbokun'][2023].count }}</td>
                    </tr>


                    <tr>
                        <td>各平均単価</td>
                        <td>{{ sales_data['jtbbokun'][2019].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2020].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2021].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2022].tanka.toLocaleString() }}</td>
                        <td>{{ sales_data['jtbbokun'][2023].tanka.toLocaleString() }}</td>
                    </tr>



                </tbody>
            </table>

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

export default {
    components: { Layout, LinkLayout },

    props: ['type_id', 'type_list' ,'category', 'sales_data', 'year', 'select_year'],


    data() {
        return {
            form: this.$inertia.form({
                type: this.type,
            }),
            loading: {}
        }
    },
    computed: {

    },

    methods: {

    }
}
</script>
  