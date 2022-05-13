<template>
  <v-container>
    <div>
      <v-alert
        dismissible
        color="purple"
        elevation="24"
        type="success"
      >
        認証が出来ました!<br>
        ユーザー情報を入力してください。
      </v-alert>
    </div>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-text-field
        v-model="name"
        :rules="name_rules"
        label="名前"
        required
      ></v-text-field>

      <v-menu
        ref="menu"
        v-model="menu"
        :close-on-content-click="false"
        transition="scale-transition"
        offset-y
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="birth_day"
            label="誕生日"
            append-outer-icon="mdi-calendar"
            v-bind="attrs" v-on="on"
          ></v-text-field>
        </template>
        <v-date-picker
          v-model="birth_day"
          color='#2ca9e1'
          elevation="15"
          @change="save"
        ></v-date-picker>
      </v-menu>

      <v-btn
        color="success"
        class="mr-4"
        @click="submit"
      >ユーザー情報を入力する
      </v-btn>

    </v-form>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    valid: true,
    name: '',
    name_rules: [
      v => !!v || '必須項目です。',
      v => (v && v.length <= 10) || '名前は10文字以内です',
    ],
    birth_day: '2000-01-01',
    menu: false,
  }),
  created: function() {
    this.$store.dispatch('hideLoading');
  },
  methods: {
    submit() {
      this.$store.dispatch('createUser', {
        name: this.name,
        birth_day: this.birth_day,
      });
    },
    save (date) {
      this.$refs.menu.save(date)
    },
  },
};

</script>

<style scoped>

</style>