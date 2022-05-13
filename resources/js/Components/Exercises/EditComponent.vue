<template>
  <v-card>
    <v-card-title>{{ exercise_type_name }}</v-card-title>

    <span v-for="exercise_detail in exercise_details" :key="exercise_detail.id">
      <v-row
        justify="center"
        align-content="center"
        align="center"
      >
        <v-col
          cols="2"
          sm="2"
          md="2"
        >
          <v-icon
            @click="copy(exercise_detail.id)"
          >
            mdi-note-plus
          </v-icon>
        </v-col>
        <v-col
          cols="3"
          sm="3"
          md="3"
        >
          <v-text-field
            type="number"
            min="0"
            label="Kg"
            v-model="exercise_detail.weight"
          ></v-text-field>
        </v-col>

        <v-col
          cols="1"
          sm="1"
          md="1"
        >
          <v-icon>mdi-close</v-icon>
        </v-col>

        <v-col
          cols="3"
          sm="3"
          md="3"
        >
          <v-text-field
            type="number"
            min="0"
            label="回"
            v-model="exercise_detail.rep"
          ></v-text-field>
        </v-col>

        <v-col
          cols="1"
          sm="1"
          md="1"
        >
          <v-btn fab dark small color="red" @click="removeInput(exercise_detail.id)">
            <v-icon dark>mdi-minus</v-icon>
          </v-btn>
        </v-col>

      </v-row>
    </span>

    <v-row
      justify="center"
      align-content="center"
      align="center"
    >
      <v-spacer></v-spacer>

      <v-col
        cols="1"
        sm="1"
        md="1"
      >

        <v-btn fab dark small color="blue" @click="addInput()">
          <v-icon dark>mdi-plus</v-icon>
        </v-btn>
      </v-col>

      <v-col
        cols="1"
        sm="1"
        md="1"
      ></v-col>
    </v-row>

    <p class="text-center">
      <v-btn
        color="success"
        class="mr-4"
        @click="update"
      >
        変更する
      </v-btn>
    </p>
  </v-card>
</template>

<script>
import router from '../../router';

export default {
  data: () => ({
    exercise_details: [],
    exercise_type_name: '',
    exercise_type_id: null,
    exercise_id: null,
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created: function() {
    this.exercise_id = this.$route.params['exercise_id'];
    axios.get('/api/exercises/' + this.exercise_id).then((res) => {
      this.exercise_details = res.data.exercise_details;
      this.exercise_type_name = res.data.exercise_name;
      this.exercise_type_id = res.data.exercise_type_id;

      this.$store.dispatch('hideLoading');

      axios.get('/api/exercise_type/' + this.exercise_type_id,).then((res) => {
        this.exercise_type_name = res.data.exercise_type.name;
      });
    });
  },
  methods: {
    addInput() {
      let exercise_details = this.exercise_details;
      exercise_details.push({
        id: this.getUniqueId(),
        rep: 0,
        weight: 0,
      });
    },
    copy(id) {
      let exercise_details = this.exercise_details;
      for (const index in exercise_details) {
        if (exercise_details[index]['id'] === id) {
          this.exercise_details[index]['id'] = this.getUniqueId();
          this.exercise_details[index]['rep'] = exercise_details[parseInt(index)-1]['rep'];
          this.exercise_details[index]['weight'] = exercise_details[parseInt(index)-1]['weight'];
          break;
        }
      }
    },
    /**
     * @param id
     */
    removeInput(id) {
      let exercise_details = this.exercise_details;
      // 消そうとしているもの以外を取り出す
      exercise_details = exercise_details.filter((input) => {
        return input.id !== id;
      });
      let new_exercise_detail = [];
      for (let i = 0; i < exercise_details.length; i++) {
        new_exercise_detail.push({
          id: this.getUniqueId(),
          rep: exercise_details[i].rep,
          weight: exercise_details[i].weight,
          text: exercise_details[i].text,
        });
      }
      this.exercise_details = new_exercise_detail;
    },
    update() {
      this.$store.dispatch('showLoading');

      axios.put('/api/exercises/' + this.exercise_id, {
        exercise_details: this.exercise_details
      }).then((res) => {
        if (res.status === 200) {
          this.$store.dispatch('setMessage', {
            message: '更新できました！',
            type: 'success',
          });
          router.push({name: 'exercises.home'});
        }
      }).finally(() => {
        this.$store.dispatch('hideLoading');
      });
    },
    getUniqueId() {
      return parseInt(new Date().getTime().toString());
    }
  },
};
</script>
