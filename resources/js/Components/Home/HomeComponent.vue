<template>

  <div>
    <div class="my-center">
      <router-link :to="{ name: 'exercises.home'}">
        <v-btn
          center
          class="add-button"
          color="primary"
        >
          <v-icon dark>mdi-plus</v-icon>
          今日のトレーニングを追加する
        </v-btn>
      </router-link>
    </div>

    <span v-if="exercise_volume.length">
      <this-week-exercise-volume-graph-component :exercise_volume="exercise_volume"></this-week-exercise-volume-graph-component>
    </span>
    <span v-else>
      <v-card-text>今週はまだトレーニングが記録されていないようです...</v-card-text>
    </span>

    <v-toolbar
      color="#4c6cb3"
      dark
      flat
    >
      <v-toolbar-title>トレーニング予定</v-toolbar-title>
      <template v-slot:extension>
        <v-tabs
          v-model="day_of_week"
        >
          <!--#c3d825 若草色 -->
          <v-tabs-slider
            color="#c3d825"
          ></v-tabs-slider>

          <v-tab
            v-for="week_day in week_days"
            :key="week_day.week_day_id"
            :value="week_day.week_day_id"
          >
            {{ week_day.week_day_name }}
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>

    <v-tabs-items v-model="day_of_week">
      <v-tab-item
        v-for="week_day in week_days"
        :key="week_day.week_day_id"
      >
        <!--#c3d825 若草色 -->
        <v-card
          color="#e8ecef"
          flat
        >
          <v-card-title>
            予定のトレーニング内容
          </v-card-title>
            <v-row>
              <v-col
                cols="5"
                lg="7"
                sm="5"
                md="5"
              ></v-col>
              <v-spacer></v-spacer>
              <v-col
                cols="2"
                lg="1"
                sm="2"
                md="2"
              >
                <v-btn @click="moveEditHome(week_day.week_day_id)">
                  <v-icon>mdi-fountain-pen-tip</v-icon>
                </v-btn>
              </v-col>
              <v-spacer></v-spacer>
              <v-col
                cols="2"
                lg="1"
                sm="2"
                md="2"
              >
                <v-dialog
                  v-model="dialog"
                  persistent
                  max-width="290"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      @click="openPlanExerciseConfirm"
                    >
                      <v-icon>mdi-content-copy</v-icon>
                    </v-btn>
                  </template>
                  <v-card>
                    <v-card-title class="headline">
                      予定のトレーニングを登録しますか？
                    </v-card-title>
                    <v-card-text>OKを押すとトレーニングが登録されます！</v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="green darken-1"
                        text
                        @click="dialog=false"
                      >
                        Cancel
                      </v-btn>
                      <v-btn
                        color="green darken-1"
                        text
                        @click="registerPlanExercise(week_day.week_day_id)"
                      >
                        OK
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-col>
              <v-spacer></v-spacer>
            </v-row>

          <div
            v-for="exercise in week_day.exercises"
            :key="exercise.exercise_id"
          >
            <v-card-subtitle >
              {{ exercise.exercise_name }}
            </v-card-subtitle>
            <v-card-text>
              <div
                v-for="exercise_detail in exercise.exercise_details"
                :key="exercise_detail.id"
                >
                {{ exercise_detail.weight }}Kg
                <v-icon>mdi-close</v-icon>
                {{ exercise_detail.rep }}Reps
              </div>
            </v-card-text>
          </div>
        </v-card>
      </v-tab-item>
    </v-tabs-items>

  </div>

</template>

<script>
import router from '../../router';
import ThisWeekExerciseVolumeGraphComponent from './ThisWeekExerciseVolumeGraphComponent';

export default {
  data: () => ({
    week_days: [],
    day_of_week: null,
    dialog: false,
    height: 100,
    exercise_volume: []
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    axios.get('/api/exercise_plans').then((res) => {
      this.week_days = res.data;
      this.$store.dispatch('hideLoading');
      // デフォルトのタブ
      this.day_of_week = this.getDayOfWeek();
    });
    axios.get('/api/exercises/week_volume').then((res) => {
      this.exercise_volume = res.data.exercise_volume;
    });
  },
  methods: {
    /**
     * 日: 0 ~ 土: 6のコードで取得
     */
    getDayOfWeek() {
      const today = new Date();
      return parseInt(today.getDay());
    },
    /**
     * 予定の筋トレ編集へ移動
     * @param {int} week_day_id
     */
    moveEditHome(week_day_id) {
      this.$store.dispatch('showLoading');
      router.push({
        name: 'exercise_plans.home',
        query: { week_day: week_day_id }
      });
    },
    /**
     * 予定の筋トレを登録するかの確認ダイアログを表示
     */
    openPlanExerciseConfirm() {
      this.dialog = true;
    },
    /**
     * 筋トレ予定を登録する
     * @param {int} week_day_id
     */
    registerPlanExercise(week_day_id) {
      this.dialog = false;
      this.$store.dispatch('showLoading');
      axios.post('/api/exercises/add_plan_exercises', {
        'week_day_id': week_day_id
      }).then((res) => {
        this.$store.dispatch('setMessage', {
          message: res.data.message,
          type: 'success',
        });
        router.push({ name: 'exercises.home' });
      });
    },
  },
  computed:{
    myStyles() {
      return{
        height: '500px',
        position:'relative'
      }
    }
  },
  components: {
    ThisWeekExerciseVolumeGraphComponent
  },
};
</script>

<style scoped>
.add-button {
  margin-bottom: 1em;
}
.my-center {
  display: flex;
  justify-content: center;
}
</style>