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
        @click="submit"
      >
        追加する
      </v-btn>
    </p>
  </v-card>
</template>

<script>
import router from '../../router';

export default {
  data: () => ({
      exercise_details: [
        {
          id: 0,
          rep: null,
          weight: null,
        },
      ],
      exercise_type_name: '',
      exercise_type_id: null,
      date: ''
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    this.exercise_type_id = this.$route.params['exercise_type_id'];
    this.date = this.$route.query['date'];
    axios.get('/api/exercise_type/' + this.exercise_type_id,
    ).then((res) => {
      this.exercise_type_name = res.data.exercise_type.name;
      this.$store.dispatch('hideLoading');
    });
  },
  methods: {
    addInput() {
      let exercise_details = this.exercise_details;
      exercise_details.push({
        id: this.getUniqueId(),
        rep: null,
        weight: null,
      });
    },
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
    submit() {
      this.$store.dispatch('showLoading');
      axios.post('/api/exercises/',
        {
          exercise_type_id: this.exercise_type_id,
          exercise_details: this.exercise_details,
          date: this.date,
        },
      ).then(() => {
        this.$store.dispatch('setMessage', {
          message: '登録できました！',
          type: 'success',
        });
        router.push({name: 'exercises.home', query: { date: this.date }});
      });
    },
    getUniqueId() {
      return parseInt(new Date().getTime().toString());
    }
  },
};
//http://hand28.hatenadiary.jp/entry/2018/12/21/181550
</script>