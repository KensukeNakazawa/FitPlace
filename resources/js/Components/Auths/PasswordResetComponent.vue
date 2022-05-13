<template>
  <v-card>
    <v-card-title>パスワード再設定</v-card-title>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >

      <v-card-text>
        <v-text-field
          v-model="new_password"
          :rules="password_rules"
          label="新しいパスワード"
          :counter="30"
          required
          :append-icon="new_password_toggle.icon"
          :type="new_password_toggle.type"
          @click:append="new_password_show = !new_password_show"
        ></v-text-field>
      </v-card-text>

      <v-card-text>
        <v-text-field
          v-model="confirm_password"
          :rules="password_rules"
          label="確認用パスワード"
          :counter="30"
          required
          :append-icon="confirm_password_toggle.icon"
          :type="confirm_password_toggle.type"
          @click:append="confirm_password_show = !confirm_password_show"
        ></v-text-field>
      </v-card-text>

      <v-card-actions>
        <v-btn
          rounded
          color="success"
          class="mr-4"
          @click="submit"
          :disabled="!can_request"
        >
          パスワードを再設定する
        </v-btn>
      </v-card-actions>

    </v-form>
  </v-card>
</template>

<script>
import router from '../../router';

export default {
  mounted() {
  },
  data: () => ({
    valid: true,
    new_password: '',
    confirm_password: '',
    password_rules: [
      v => !!v || '必須項目です',
      v => /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,50}$/.test(v) || '半角英数字6文字以上です。',
    ],
    new_password_show: false,
    confirm_password_show: false,
    auth_code: null,
    can_request: false
  }),
  created() {
    this.auth_code = this.$route.query['auth_code'];
  },
  computed: {
    new_password_toggle() {
      const icon = this.new_password_show ? 'mdi-eye' : 'mdi-eye-off';
      const type = this.new_password_show ? 'text' : 'password';
      return {icon, type};
    },
    confirm_password_toggle() {
      const icon = this.confirm_password_show ? 'mdi-eye' : 'mdi-eye-off';
      const type = this.confirm_password_show ? 'text' : 'password';
      return {icon, type};
    },
  },
  watch: {
    /**
     * emailを監視し、バリデーションが通っていれば、ボタンを押せるようにする
     */
    new_password: function() {
      this.can_request = this.$refs.form.validate();
    },
    confirm_password: function() {
      this.can_request = this.$refs.form.validate();
    },
  },
  methods: {
    submit() {
      /** 新しいパスワードと確認用のパスワードが同じであること */
      if (this.new_password !== this.confirm_password) {
        this.$store.dispatch('setMessage', {
          message: '新しいパスワードと確認用パスワードは同じものを入力してください',
          type: 'error',
        });
        return
      }

      this.$store.dispatch('showLoading');

      axios.put('/api/auth/password_reset/reset', {
        auth_code: this.auth_code,
        new_password: this.new_password
      }).then((res) => {
        this.$store.dispatch('setMessage', {
          message: 'パスワードを再設定しました。ログインしてください。',
          type: 'success',
        });
        router.push({ name: 'auth.login' });
        this.$store.dispatch('hideLoading');
      });
    },
  }
}
</script>

<style scoped>

</style>