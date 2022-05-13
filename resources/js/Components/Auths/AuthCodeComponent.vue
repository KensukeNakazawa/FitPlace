<template>
  <v-card>
    <div>
      <v-alert
        dismissible
        color="purple"
        elevation="24"
        type="success"
      >メールを送信しました。送信された認証コードを入力してください。
      </v-alert>
    </div>

    <v-card-title>認証コード</v-card-title>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-card-text>
        <v-text-field
          v-model="auth_code"
          :rules="auth_code_rules"
          label="認証コード"
          required
        ></v-text-field>
      </v-card-text>

      <v-card-actions>
        <v-btn
          color="success"
          class="mr-4"
          @click="submit"
          :disabled="!can_request"
        >
          コードを認証する
        </v-btn>
      </v-card-actions>
    </v-form>
  </v-card>
</template>

<script>
// https://github.com/vuetifyjs/vuetify/blob/master/packages/docs/src/examples/v-form/misc-validation-with-submit-and-clear.vue

export default {
  data: () => ({
    valid: true,
    auth_code: '',
    auth_code_rules: [
      v => !!v || '必須項目です。',
      v => (v && v.length === 5) || '認証コードは5桁です。',
    ],
    show: false,
    can_request: false,
  }),
  created: function() {
    this.$store.dispatch('hideLoading');
  },
  watch: {
    auth_code: function() {
      this.can_request = this.$refs.form.validate();
    }
  },
  methods: {
    /**
     * 認証コードを認証する
     */
    submit() {
      this.$store.dispatch('authCode', {
        auth_id: localStorage.auth_id,
        auth_code: this.auth_code,
      });
    },
  },
};
</script>
