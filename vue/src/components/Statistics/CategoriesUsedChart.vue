<script>
import { Bar } from "vue3-chart-v2";

export default {
  name: "CategoriesUsed",
  extends: Bar,
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
};
</script>