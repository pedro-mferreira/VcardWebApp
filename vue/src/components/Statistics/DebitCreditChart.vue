<script>
import { Doughnut } from "vue3-chart-v2";

export default {
  name: "DebitCreditChart",
  extends: Doughnut,
  props: {
    chartData: {
      type: Object,
      required: true,
    },
    chartOptions: {
      type: Object,
      required: false,
    },
  },
  mounted() {
    this.renderChart(this.chartData, this.chartOptions);
  },
   watch: {
    chartData: function(){
      this.renderChart(this.chartData, this.chartOptions)
    }
  },
};
</script>