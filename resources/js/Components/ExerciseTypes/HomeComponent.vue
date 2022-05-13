<template>

  <div>

    <v-banner><h3>トレーニング種目一覧</h3></v-banner>

    <v-card
      v-for="body_part in body_parts"
      :key="body_part.body_part_id"
      class="body-part-item"
    >
      <v-card-title>{{ body_part.body_part_name }}</v-card-title>

      <v-list-item-group
        color="primary"
      >
        <v-list-item
          v-for="exercise_type in body_part.exercise_types"
          :key="exercise_type.exercise_type_id"
          @click="moveLink('exercise_types.past_exercises', {'exercise_type_id': exercise_type.exercise_type_id})"
        >
          <v-list-item-icon>
            <v-icon>mdi-history</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="exercise_type.exercise_type_name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>

    </v-card>

    <div @click="showModal">
      <plus-button-component></plus-button-component>
    </div>

    <modal
      name="body-part-modal"
      hight="auto"
      width="85%"
    >
      <form>
        <v-card-text>
          <v-select
            item-text="name"
            item-value="id"
            :items="selectable_body_parts"
            label="部位"
            @change="selectBodyPart"
            required
          ></v-select>
        </v-card-text>

        <v-card-text>
          <v-text-field
            v-model="exercise_type_name"
            label="トレーニング種目"
            required
          ></v-text-field>
        </v-card-text>

        <v-card-text>
          <v-btn
            color="success"
            class="mr-4"
            @click="addExerciseType"
          >
            トレーニングを追加
          </v-btn>
        </v-card-text>
      </form>
    </modal>
  </div>

</template>

<script>
import PlusButtonComponent from '../LayoutItems/PlusButton';
import router from '../../router';
export default {
  components: {
    PlusButtonComponent
  },
  data: () => ({
    body_parts: null,
    selectable_body_parts: null,
    selected_body_part: null,
    exercise_type_name: ''
  }),
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    axios.get('/api/me/exercise_types').then((res) => {
      this.body_parts = res.data;
      this.$store.dispatch('hideLoading');
    });
  },
  methods: {
    showModal() {
      this.$modal.show('body-part-modal');
      axios.get('/api/body_parts').then((res) => {
        this.selectable_body_parts = res.data;
      });
    },
    /**
     * モーダルで選んだ部位をリクエスト様のbody_partにセットする
     * @param {int} body_part_id
     */
    selectBodyPart(body_part_id) {
      const selected_body_part = this.selectable_body_parts.filter((body_part) => {
        return body_part.id === body_part_id
      });
      this.selected_body_part = selected_body_part[0];
    },
    /**
     *　トレーニング種目を追加する
     *    apiでDBに追加
     *    フロント側の表示切り替え
     */
    addExerciseType() {
      const selected_body_part = this.selected_body_part
      this.$store.dispatch('showLoading');
      axios.post('/api/body_parts', {
        'body_part_id': selected_body_part.id,
        'exercise_type_name': this.exercise_type_name
      }).then((res) => {
        const exercise_type = res.data.exercise_type;
        const response_message = res.data.message;

        /** 追加した種目を表示側に追加する */
        let new_body_parts =this.body_parts;
        for (let i = 0; i < new_body_parts.length; i++) {
          if (new_body_parts[i].body_part_id === selected_body_part.id) {
            new_body_parts[i]['exercise_types'].push({
              'exercise_type_id': exercise_type.id,
              'exercise_type_name': exercise_type.name
            });
          }
        }
        this.body_parts = new_body_parts;

        this.$store.dispatch('hideLoading');
        this.$store.dispatch('setMessage', {
          message: response_message,
          type: 'success',
        });
        this.$modal.hide('body-part-modal');
      });
    },
    moveLink(move_to, params) {
      router.push({
        name: move_to,
        params: params
      });
    }
  }
}
</script>

<style scoped>
.body-part-item {
  margin-bottom: 0.5em;
}

</style>