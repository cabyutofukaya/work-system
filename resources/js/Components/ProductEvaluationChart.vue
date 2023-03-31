<script>
import {Pie} from 'vue-chartjs'
import chartjsPluginDatalabels from 'chartjs-plugin-datalabels'

export default ({
  extends: Pie,

  props: {
    'labels': Array,
    'count': Array,
  },

  data() {
    return {
      data: {
        // 凡例とツールチップに表示するラベル
        labels: this.labels,
        // 表示するデータ
        datasets: [
          {
            data: this.count,
            backgroundColor: ['#4CAF50', '#8BC34A', '#FF9800', '#F44336', '#9C27B0', '#9E9E9E']
          }
        ]
      },
      options: {
        responsive: false,
        legend: {
          display: false,
          // position: "right"
        },
        plugins: {
          datalabels: {
            formatter: function (value, context) {
              // 値が0ならラベルを出力しない
              if (!value) {
                return "";
              }

              const label = context.chart.data.labels[context.dataIndex]

              let sum = 0;
              let dataArr = context.chart.data.datasets[0].data;
              dataArr.map(data => {
                sum += data;
              });
              let percentage = (value * 100 / sum).toFixed(2) + "%";

              return `${label} ${percentage} (${value})`
            },
            font: {
              // size: 16,
              // weight: 'bold',
            },
            color: 'white',
            textStrokeColor: 'black',
          }
        }
      }
    }
  },

  mounted() {
    this.addPlugin(chartjsPluginDatalabels)
    this.renderChart(this.data, this.options)
  }

})
</script>
