<script>
import { Doughnut } from 'vue-chartjs'

export default ({
  extends: Doughnut,
  props: {
    exercise_volume: Array
  },
  data: () => ({

    // 凡例とツールチップに表示するラベル
    chart_data: {
      labels: [],
      // 表示するデータ
      datasets: [
        {
          data: [],
          backgroundColor: ['#4c6cb3', '#007bbb', '#89c3eb', '#abced8', '#dbd0e6', '#a0d8ef', '#2ca9e1', '#eaf4fc'],
          borderColor: 'transparent'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'right'
      },
      title: {
        // タイトル
        // See https://misc.0o0o.org/chartjs-doc-ja/configuration/title.html
        display: true, // タイトルを表示します。
        position: "top", // タイトルの位置
        fontSize: 14, // タイトルのフォントサイズ
        text: "今週のトレーニングボリューム"
      },
    }
  }),
  created() {
    axios.get('/api/exercises/week_volume').then((res) => {
      const exercise_volumes = this.exercise_volume;
      if (exercise_volumes.length) {
        for (const exercise_volume of exercise_volumes) {
          this.chart_data.labels.push(exercise_volume.name);
          this.chart_data.datasets[0].data.push(exercise_volume.volume);
        }
        this.addPlugin({
          afterDraw(chart, go) {
            let ctx = chart.ctx
            chart.data.datasets.forEach((dataset, i) => {
              let dataSum = 0
              dataset.data.forEach((element) => {
                dataSum += element
              })

              let meta = chart.getDatasetMeta(i)
              if (!meta.hidden) {
                meta.data.forEach(function(element, index) {
                  // フォントの設定
                  let fontSize = 12
                  let fontStyle = 'normal'
                  let fontFamily = 'Helvetica Neue'
                  ctx.fillStyle = '#000'
                  // 設定を適用
                  ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily)

                  // ラベルをパーセント表示に変更
                  let labelString = chart.data.labels[index]
                  let dataString = dataset.data[index]

                  // positionの設定
                  ctx.textAlign = 'center'
                  ctx.textBaseline = 'middle'

                  let padding = -2
                  let position = element.tooltipPosition()
                  // ツールチップに変更内容を表示
                  ctx.fillText(labelString, position.x, position.y - (fontSize / 2) - padding) // title
                  ctx.fillText(dataString, position.x, position.y + (fontSize / 2) - padding)  // データの百分率

                  // 凡例の位置調整
                  let legend = chart.legend
                  legend.top = chart.height - (legend.height / 2) - (legend.top / 2)
                })
              }
            })
          }
        })
        this.renderChart(this.chart_data, this.options);
      }
    });
  }
});

</script>