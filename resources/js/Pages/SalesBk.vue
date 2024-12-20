<template>
  <SalesLayout>

    <table style="width: 100%;">
      <thead>
        <tr>
          <th class="text-left">2023年</th>
          <th class="text-left">1月</th>
          <th class="text-left">2月</th>
          <th class="text-left">3月</th>
          <th class="text-left">4月</th>
          <th class="text-left">5月</th>
          <th class="text-left">6月</th>
          <th class="text-left">7月</th>
          <th class="text-left">8月</th>
          <th class="text-left">9月</th>
          <th class="text-left">10月</th>
          <th class="text-left">11月</th>
          <th class="text-left">12月</th>
        </tr>
      </thead>
      <tbody>

        <tr>
          <td>予算</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>売上</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>件数</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>平均単価</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>前年売上</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>予算比(額)</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

        <tr>
          <td>前年比（額)</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>


        <tr>
          <td>予算比（率)</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>


        <tr>
          <td>前年比（率)</td>
          <td>10000</td>
          <td>20000</td>
          <td>30000</td>
          <td>40000</td>
          <td>50000</td>
          <td>60000</td>
          <td>70000</td>
          <td>80000</td>
          <td>90000</td>
          <td>100000</td>
          <td>110000</td>
          <td>120000</td>
        </tr>

      </tbody>
    </table>


  </SalesLayout>
</template>

<style>
table {
  width: 90%;
  border-collapse: collapse;
}

th {
  border-top: 1px solid #333;
  border-bottom: 1px solid #333;
  background: rgb(126, 154, 238);
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
import SalesLayout from "./SalesLayout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { SalesLayout, Link },

  // props: ['user'],

  data() {
    return {
      headers: [
        {
          title: 'Dessert (100g serving)',
          align: 'start',
          sortable: false,
          key: 'name',
        },
        { title: 'Calories', align: 'end', key: 'calories' },
        { title: 'Fat (g)', align: 'end', key: 'fat' },
        { title: 'Carbs (g)', align: 'end', key: 'carbs' },
        { title: 'Protein (g)', align: 'end', key: 'protein' },
        { title: 'Iron (%)', align: 'end', key: 'iron' },
      ],
      desserts: [
        {
          name: 'Frozen Yogurt',
          calories: 159,
          fat: 6.0,
          carbs: 24,
          protein: 4.0,
          iron: '1',
        },
        {
          name: 'Ice cream sandwich',
          calories: 237,
          fat: 9.0,
          carbs: 37,
          protein: 4.3,
          iron: '1',
        },
        {
          name: 'Eclair',
          calories: 262,
          fat: 16.0,
          carbs: 23,
          protein: 6.0,
          iron: '7',
        },
        {
          name: 'Cupcake',
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          iron: '8',
        },
        {
          name: 'Gingerbread',
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          iron: '16',
        },
        {
          name: 'Jelly bean',
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          iron: '0',
        },
        {
          name: 'Lollipop',
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          iron: '2',
        },
        {
          name: 'Honeycomb',
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          iron: '45',
        },
        {
          name: 'Donut',
          calories: 452,
          fat: 25.0,
          carbs: 51,
          protein: 4.9,
          iron: '22',
        },
        {
          name: 'KitKat',
          calories: 518,
          fat: 26.0,
          carbs: 65,
          protein: 7,
          iron: '6',
        },
      ],
      loading: {}
    }
  },
  computed: {
    virtualDesserts() {
      return [...Array(10).keys()].map(i => {
        const dessert = { ...this.desserts[i % 10] }
        dessert.name = `${dessert.name} #${i}`

        return dessert
      })
    },
  },

  methods: {

  }
}
</script>
