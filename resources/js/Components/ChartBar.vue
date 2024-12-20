<script>
import { Bar, mixins } from 'vue-chartjs'
import chartjsPluginAnnotation from 'chartjs-plugin-annotation'
import chartjsPluginDatalabels from 'chartjs-plugin-datalabels'

const { reactiveProp } = mixins

export default {
  name: 'ChartBar',
  extends: Bar,
  mixins: [reactiveProp],
  props: {
    chartData: { type: Object, required: true, default: () => null },
    options: { type: Object, required: false, default: () => null }
  },
  mounted () {
    this.addPlugin([chartjsPluginAnnotation,chartjsPluginDatalabels])
    this.renderChart(this.chartData, this.options)
  }
}
</script>

<style scoped></style>