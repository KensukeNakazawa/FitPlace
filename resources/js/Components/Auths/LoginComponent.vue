<template>
  <v-card>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-card-title>ログイン</v-card-title>

      <v-card-text>
        <v-text-field
          v-model="email"
          :rules="emailRules"
          label="メールアドレス"
          required
        ></v-text-field>
      </v-card-text>

      <v-card-text>
        <v-text-field
          v-model="password"
          :append-icon="toggle.icon"
          :type="toggle.type"
          @click:append="show = !show"
          :rules="passwordRules"
          :counter="30"
          label="パスワード"
          required
        ></v-text-field>
      </v-card-text>

      <v-card-text>
        <router-link :to="{ name: 'auth.password_reset.send_mail' }">
          パスワードを忘れた方はこちら
        </router-link>
      </v-card-text>

      <v-row>
        <v-col cols="2"></v-col>
        <v-col
          cols="3"
          class="my-center"
        >
          <v-card-actions>
            <v-btn
              rounded
              color="success"
              class="mr-4"
              @click="submit"
              :disabled="!can_request"
            >
              ログイン
            </v-btn>
          </v-card-actions>
        </v-col>

        <v-col
          cols="5"
          class="my-center"
        >
          <v-card-actions>
            <v-btn
              color="green darken-1"
              text
            >
              <router-link :to="{ name: 'auth.register'}">会員登録はこちら</router-link>
            </v-btn>
          </v-card-actions>
        </v-col>

        <v-col cols="2"></v-col>

      </v-row>

    </v-form>

<!--        <img src="/images/sign_in_twitter.png" alt="twitter_sign_in">-->
    <v-card-text align="center">SNSでログイン</v-card-text>

    <twitter-connect-button></twitter-connect-button>

  </v-card>
</template>

<script>
import TwitterConnectButton from '../LayoutItems/TwitterConnectButton';
// https://github.com/vuetifyjs/vuetify/blob/master/packages/docs/src/examples/v-form/misc-validation-with-submit-and-clear.vue
export default {
  data: () => ({
    valid: true,
    email: '',
    emailRules: [
      v => !!v || 'メールアドレスは必須項目です。',
      v => /.+@.+\..+/.test(v) || 'メールアドレスの形式が誤っています。',
    ],
    password: '',
    passwordRules: [
      v => !!v || 'パスワードは必須項目です',
    ],
    show: false,
    can_request: false,
  }),
  components: {
    TwitterConnectButton
  },
  computed: {
    toggle() {
      const icon = this.show ? 'mdi-eye' : 'mdi-eye-off';
      const type = this.show ? 'text' : 'password';
      return {icon, type};
    },
  },
  created: function() {
    this.$store.dispatch('hideLoading');
  },
  watch: {
    email: function() {
      this.can_request = this.$refs.form.validate();
    },
    password: function() {
      this.can_request = this.$refs.form.validate();
    }
  },
  methods: {
    /**
     * ログインする
     */
    submit() {
      this.$store.dispatch('login', {
        email: this.email,
        password: this.password,
      });
    },
  },
};
</script>

<style scoped>
.my-center {
  display: flex;
  justify-content: center;
}
</style>