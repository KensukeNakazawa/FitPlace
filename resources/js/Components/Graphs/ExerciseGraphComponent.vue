<template>
  <v-container>
    <div class="small">
      <line-chart :chart-data="data_collection"></line-chart>
      <button @click="fillData()">Randomize</button>
    </div>
  </v-container>

</template>
<script>
import LineChart from './LineChart';

export default {
  components: {
    LineChart
  },
  data: () => ({
    data_collection: null
  }),
  mounted() {
    this.fillData()
  },
  methods: {
    fillData() {
      this.data_collection = {
        labels: ["2021-05", "2021-06", "2021-07", "2021-08", "2021-09", "2021-10"],
        datasets: [
          {
            label: 'Data One',
            fill: false,
            data: [1, 2, 4, 10, 7, 9],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
          },
        ]
      }
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5
    }
  }
}
</script>

<style scoped>
.small {
  max-width: 600px;
  margin: 150px auto;
}
</style>