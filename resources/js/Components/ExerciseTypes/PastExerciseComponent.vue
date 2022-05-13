<template>
<div>

  <v-card
    class="exercise-title"
  >
    <v-card-title>{{ exercise_type.name }}</v-card-title>
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title>1RM {{ exercise_type.max_weight }}Kg</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </v-card>

  <v-card
    v-for="exercise in exercises"
    :key="exercise.id"
    class="exercise-item"
  >
    <v-card-title>
      <v-icon>mdi-calendar-range</v-icon>{{ formatShowDate(exercise.exercise_at) }}
    </v-card-title>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title>1RM {{ exercise.max }}Kg</v-list-item-title>
        <v-list-item-subtitle>トレーニングボリューム{{ exercise.volume }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>

    <v-card-text>
      <div
        v-for="exercise_detail in exercise.exercise_details"
        :key="exercise_detail.id"
      >
        {{ exercise_detail.weight }}Kg
        <v-icon>mdi-close</v-icon>
        {{ exercise_detail.rep }}rep
      </div>
    </v-card-text>
  </v-card>

  <infinite-loading @infinite="infiniteHandler" spinner="spiral">
    <div slot="spinner">
      <vue-loading type="spiningDubbles" color="#4c6cb3"></vue-loading>
    </div>
    <div slot="no-more"><v-divider></v-divider></div>
    <div slot="no-results" class="note-item"><v-divider></v-divider></div>
  </infinite-loading>

</div>
</template>

<script>
import {VueLoading} from 'vue-loading-template';
import moment from 'moment';
export default {
  components: {
    VueLoading,
  },
  data: () => ({
    exercise_type: null,
    exercises: null,
    page: 1
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    const exercise_type_id = this.$route.params['exercise_type_id'];
    axios.get('/api/exercise_types/past_exercises/' + exercise_type_id, {
      params: { 'page': this.page }
    }).then((res) => {
      const response = res.data;
      this.exercise_type = response.exercise_type;
      this.exercises = response.exercises;
    }).finally(() => {
      this.$store.dispatch('hideLoading');
    });
  },
  methods: {
    formatShowDate(date) {
      return moment(date).format('YYYY年M月DD日');
    },
    /**
     * 無限スクロール
     * @param $state
     */
    infiniteHandler($state) {
      const exercise_type_id = this.$route.params['exercise_type_id'];
      this.page += 1;
      axios.get('/api/exercise_types/past_exercises/' + exercise_type_id, {
        params: { 'page': this.page }
      }).then((res) => {
        const exercises = res.data.exercises;
        if (exercises.length === 0) {
          $state.complete();
        } else {
          for (const exercise of exercises) {
            this.exercises.push(exercise);
          }
          $state.loaded();
        }
      });
    }
  }
}
</script>

<style scoped>
.exercise-title {
  margin-bottom: 1em;
}
.exercise-item {
  margin-bottom: 0.5em;
}
.note-item {
  margin-top: 1em;
}
</style>