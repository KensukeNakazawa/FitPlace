<template>
  <v-card>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-card-text>登録しているメールアドレスを入力してください</v-card-text>
      <v-card-text>
        入力したメールアドレスにパスワード再設定用のURLを送信します。<br>
        メールに記載されているURLにアクセスし、パスワードの再設定を行ってください。<br>
        ※パスワード再設定用のURLの有効期限は20分です。
      </v-card-text>

      <v-card-text>
        <v-text-field
          v-model="email"
          :rules="email_rules"
          label="メールアドレス"
          required
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
          送信する
        </v-btn>
      </v-card-actions>

    </v-form>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    valid: true,
    can_request: false,
    email: '',
    email_rules: [
      v => !!v || 'メールアドレスは必須項目です。',
      v => /.+@.+\..+/.test(v) || 'メールアドレスの形式が誤っています。',
    ],
  }),
  watch: {
    /**
     * emailを監視し、バリデーションが通っていれば、ボタンを押せるようにする
     */
    email: function() {
      this.can_request = this.$refs.form.validate();
    },
  },
  methods: {
    /**
     * パスワード再設定用のメールを送信する
     * バリデーションに失敗した時のみエラーメッセージを表示
     * セキュリティの都合上でメールアドレスが存在しない時は正常の処理とする
     */
    submit() {
      this.$store.dispatch('showLoading');
      axios.post('/api/auth/password_reset/send_mail', {
        email: this.email
      }).then(() => {        this.$store.dispatch('setMessage', {
          message: 'パスワード再設定用のメールを送信しました。',
          type: 'success',
        });
        this.$store.dispatch('hideLoading');
      });
    }
  },
}
</script>

<style scoped>

</style>