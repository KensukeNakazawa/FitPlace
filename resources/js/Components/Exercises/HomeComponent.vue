<template>
  <div>

    <div class="date-selector">
      <span>
        <v-btn
          small
          @click="beforeDate"
        >
          <v-icon>mdi-arrow-left-bold</v-icon>
        </v-btn>

        <v-menu>
          <template v-slot:activator="{ on, attrs }">
            <span class="show-date" v-bind="attrs" v-on="on">{{ show_date }}</span>
          </template>
          <v-date-picker
            color='#2ca9e1'
            elevation="15"
            v-model="date"
            @input="inputDate(date)"
          ></v-date-picker>
        </v-menu>

        <v-btn
          small
          @click="nextDate"
        >
          <v-icon>mdi-arrow-right-bold</v-icon>
        </v-btn>
      </span>

    </div>

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
                <v-card-title>{{ exercise.exercise_name }}</v-card-title>
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
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </template>

                  <v-card>
                    <v-card-title class="headline">
                      トレーニングを削除しますか？
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
                        text
                        @click="deleteExercise(delete_target_id)"
                      >
                        OK
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>

              </v-col>
              <v-spacer></v-spacer>
            </v-row>
            <v-card-subtitle>
              {{ exercise.set }} Sets
              1RM {{ exercise.exercise_max_weight }}Kg
            </v-card-subtitle>
            <v-card-text>
              <div v-for="exercise_detail in exercise.exercise_details">
                  {{ exercise_detail.weight }}Kg
                  <v-icon>mdi-close</v-icon>
                  {{ exercise_detail.rep }}Reps
              </div>
            </v-card-text>
            <v-card-actions>
              <v-btn
                @click="moveLink('exercises.edit', {exercise_id: exercise.exercise_id})"
              >
                <v-icon>mdi-pencil-box-multiple</v-icon>
              </v-btn>

              <v-btn
                @click="moveLink('exercise_types.past_exercises', {'exercise_type_id': exercise.exercise_type_id})"
              >
                <v-icon>mdi-history</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
<!--    </div>-->
        </v-col>
      </draggable>
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
          v-model="body_part.active"
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
            link
          >
            <button>
              <v-list-item-content
                @click="addExercise(date, exercise_type.exercise_type_id)"
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
import moment from 'moment';
import draggable from 'vuedraggable'
import router from '../../router';
import PlusButtonComponent from '../LayoutItems/PlusButton';

export default {
  components: {
    PlusButtonComponent,
    draggable
  },
  data: () => ({
    body_parts: [],
    exercises: [],
    date: moment(new Date).format('YYYY-MM-DD'),
    show_date: moment(new Date).format('YYYY年M月DD日'),
    dialog: false,
    delete_target_id: null,
    date_format_ja: 'YYYY年M月DD日',
    date_format_en: 'YYYY-MM-DD',
    draggable_options: {
      group: "myGroup",
      animation: 200,
      delay: 200
    },
  }),
  created: function() {
    const query_date = this.$route.query['date'];
    if (query_date) {
      this.date = moment(new Date(this.$route.query['date'])).format(this.date_format_en);
    }

    this.show_date = moment(this.date).format(this.date_format_ja)
    this.getExercise();
  },
  methods: {
    getExercise() {
      this.$store.dispatch('showLoading');
      axios.get('/api/exercises', {
        params: {
          date: this.formatDBDate(this.date),
        }
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
    addExercise(date, exercise_type_id) {
      this.$store.dispatch('showLoading');
      axios.get('/api/exercises/check_exist', {
        params: {
          exercise_type_id: exercise_type_id,
          date: this.formatDBDate(this.date)
        }
      }).then((res) => {
        const exercise = res.data.exercise;
        if (exercise !== null) {
          this.moveLink('exercises.edit', {exercise_id: exercise.id});
        } else {
          router.push({
            name: 'exercises.add',
            params: {exercise_type_id: exercise_type_id},
            query: {date: this.formatDBDate(date)}
          });
        }
      });
    },
    /**
     *
     * @param {int} exercise_id
     */
    deleteExercise(exercise_id) {
      this.$store.dispatch('showLoading');
      this.dialog = false;
      axios.delete('/api/exercises/' + exercise_id, {

      }).then(() => {
          let exercises = this.exercises;
          // 消そうとしているもの以外を取り出す
          exercises = exercises.filter((input) => {
            return input.exercise_id !== exercise_id;
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
        }
      );
    },
    hide() {
      this.$modal.hide('exercise-modal');
    },
    /**
     * 入力された日付に対して、トレーニングの更新とクエリパラメータの更新を行う
     */
    inputDate(input_date) {
      this.date = input_date;
      this.show_date = this.formatShowDate(this.date);
      this.$router.push({query: { date: this.date } });
      this.getExercise();
    },
    beforeDate() {
      this.inputDate(this.addDate(this.date, -1));
    },
    nextDate() {
      this.inputDate(this.addDate(this.date, 1));
    },
    addDate(date, amount) {
      return moment(new Date(date)).add(amount, 'd').format(this.date_format_en)
    },
    formatShowDate(date) {
      return moment(date).format(this.date_format_ja);
    },
    formatDBDate(date) {
      return moment(date).format(this.date_format_en);
    },
    openDeleteConfirm(exercise_id) {
      this.delete_target_id = exercise_id
      this.dialog = true;
    },
    /**
     * @param move_to
     * @param params
     */
    moveLink(move_to, params=null) {
      router.push({
        name: move_to,
        params: params
      });
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
            'update_sort_index': i + 1,
          },
        );
      }
      axios.put('/api/exercises/sort/', {
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
.date-selector {
  text-align: center;
  margin-bottom: 1em;
}
.show-date {
  vertical-align: middle;
  text-align: center;
}
.delete-exercise {
  text-align: right;
  margin-top: 0.5em;
  margin-left: 2.5em;
}

.draggable-list-item {
  margin: auto auto -1.5em auto;
}
</style>