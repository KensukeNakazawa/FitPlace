<template>
  <div>

    <v-select
      v-model="select_week_day"
      :items="week_days"
      item-text="name"
      item-value="id"
      label="week day"
      @change="selectWeekDay"
      background-color="#eaf4fc"
      filled
      dense
    ></v-select>

    <v-spacer></v-spacer>
    <v-container>
        <draggable
          dense
          tag="v-row"
          v-model="exercises"
          group="myGroup"
          @start="onDrag"
          @end="onSorted"
          :options="draggable_options">

          <v-col
            v-for="exercise in exercises"
            :key="exercise.id"
            class="draggable-list-item"
            sm="6"
            md="6"
            lg="4"
            xl="3"
            cols="12"
          >
            <v-card
              outlined
            >
              <v-row>
                <v-col
                  cols="7"
                  sm="7"
                  md="7"
                >
                  <v-card-text>
                    {{ exercise.exercise_name }} ({{ exercise.set }} Sets)
                  </v-card-text>
                </v-col>
                <v-col
                  cols="3"
                  sm="3"
                  md="3"
                  class="delete-exercise"
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
                        @click="openDeleteConfirm(exercise.exercise_id)"
                      >
                        <v-icon size="20px">mdi-delete</v-icon>
                      </v-btn>
                    </template>

                    <v-card>
                      <v-card-title class="headline">
                        予定のトレーニングを削除しますか？
                      </v-card-title>
                      <v-card-text>OKを押すとトレーニングが削除されます。</v-card-text>
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
                          size="x-small"
                          text
                          @click="deleteExercise(delete_target_id)"
                        >
                          OK
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-col>
              </v-row>
              <v-card-text>
                <span v-for="exercise_detail in exercise.exercise_details">
                  {{ exercise_detail.weight }}Kg
                  <v-icon>mdi-close</v-icon>
                  {{ exercise_detail.rep }}Reps<br>
                </span>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  @click="editExercise(exercise.exercise_id)"
                >
                  <v-icon>mdi-pencil-box-multiple</v-icon>
                </v-btn>
              </v-card-actions>

            </v-card>
          </v-col>
        </draggable>
<!--      </v-row>-->
    </v-container>

    <v-card-text
      @click="openAddExerciseModal"
    >
      <plus-button-component></plus-button-component>
    </v-card-text>

    <modal
      name="exercise-modal"
      scrollable
      width="85%"
      height="auto">
      <v-list>
        <v-list-group
          v-for="body_part in body_parts"
          :key="body_part.body_part_id"
          no-action
        >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="body_part.body_part_name"></v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item
            v-for="exercise_type in body_part.exercise_types"
            :key="exercise_type.exercise_type_id"
          >
            <button>
              <v-list-item-content
                @click="addExercise(week_day_id, exercise_type.exercise_type_id)"
              >
                <v-list-item-title v-text="exercise_type.exercise_type_name"></v-list-item-title>
              </v-list-item-content>
            </button>
          </v-list-item>
        </v-list-group>
      </v-list>
    </modal>
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import router from '../../router';
import PlusButtonComponent from '../LayoutItems/PlusButton';

export default {
  components: {
    PlusButtonComponent,
    draggable,
  },
  data: () => ({
    body_parts: [],
    exercises: [],
    week_day_id: null,
    week_days: null,
    select_week_day: null,
    dialog: false,
    delete_target_id: null,
    draggable_options: {
      group: 'myGroup',
      animation: 200,
      delay: 200
    },
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    this.week_day_id = this.$route.query['week_day'];

    axios.get('/api/week_days').then((res) => {
      const week_days = res.data;
      this.week_days = week_days;
      this.select_week_day = week_days[parseInt(this.week_day_id) - 1];
    });

    this.getExercisePlan(this.week_day_id);
  },
  methods: {
    /**
     * トレーニングプランを取得する
     * @parma {int} week_day_id
     */
    getExercisePlan(week_day_id) {
      axios.get('/api/exercise_plans/exercises', {
        params: {
          week_day_id: week_day_id,
        },
      }).then((res) => {
        this.exercises = res.data;
        this.$store.dispatch('hideLoading');
      });
    },
    openAddExerciseModal() {
      this.$store.dispatch('showLoading');
      axios.get('/api/me/exercise_types').then((res) => {
        this.body_parts = res.data;
        this.$modal.show('exercise-modal');
        this.$store.dispatch('hideLoading');
      });
    },
    editExercise(exercise_id) {
      router.push({
        name: 'exercise_plans.edit',
        params: {plan_exercise_id: exercise_id},
        query: {week_day: this.week_day_id},
      });
    },
    /**
     *
     * @param {int} plan_exercise_id
     */
    deleteExercise(plan_exercise_id) {
      this.$store.dispatch('showLoading');
      this.dialog = false;
      axios.delete('/api/exercise_plans/' + plan_exercise_id, {}).then(() => {
          let exercises = this.exercises;
          // 消そうとしているもの以外を取り出す
          exercises = exercises.filter((input) => {
            return input.exercise_id !== plan_exercise_id;
          });
          let new_exercises = [];
          for (const index in exercises) {
            new_exercises.push(exercises[index]);
          }
          this.exercises = new_exercises;

          this.$store.dispatch('setMessage', {
            message: '削除しました',
            type: 'success',
          });
          this.$store.dispatch('hideLoading');
        },
      );
    },
    hide() {
      this.$modal.hide('exercise-modal');
    },
    addExercise(week_day_id, exercise_type_id) {
      this.$store.dispatch('showLoading');
      router.push({
        name: 'exercise_plans.add',
        params: {exercise_type_id: exercise_type_id},
        query: {week_day: this.week_day_id},
      });
    },
    openDeleteConfirm(exercise_id) {
      this.delete_target_id = exercise_id;
      this.dialog = true;
    },
    /**
     * 曜日を変更した時
     * @param {int} week_day_id
     */
    selectWeekDay(week_day_id) {
      this.select_week_day = this.week_days[week_day_id - 1];
      this.week_day_id = week_day_id;
      this.$store.dispatch('showLoading');
      this.getExercisePlan(week_day_id);
    },
    onDrag(event) {
      console.log("on drag: ")
      console.table(event.item)
      console.log(event.oldIndex)
    },
    onSorted() {
      var sorted_exercise = [];
      for (let i = 0; i < this.exercises.length; i++) {
        sorted_exercise.push(
          {
            'exercise_id': this.exercises[i].exercise_id,
            'sort_index': this.exercises[i].sort_index,
            'update_sort_id': i + 1,
          },
        );
      }
      axios.put('/api/exercise_plans/exercises/sort/' + this.week_day_id, {
        'exercises': sorted_exercise,
      }).then((res) => {
        for (let i = 0; i < this.exercises.length; i++) {
          this.exercises[i].sort_index = i + 1;
        }
      });
    },
  },
};
</script>

<style scoped>
.delete-exercise {
  text-align: right;
  margin-top: 0.5em;
  margin-left: 2.5em;
}

.draggable-list-item {
  margin: auto auto -1.5em auto;
}

</style>