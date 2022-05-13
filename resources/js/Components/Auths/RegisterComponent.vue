<template>
  <v-card>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-card-title>会員登録</v-card-title>
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
        利用規約を最後まで読んで同意してください
      </v-card-text>

      <v-container
        id="scroll-target"
        style="max-height: 200px; border: 0.1px solid #0094c8; font-size: 0.5%"
        class="overflow-y-auto"
      >
        <v-row
          v-scroll:#scroll-target="onScroll"
          align="center"
          justify="center"
          style="height: 1000px;"
        >
          <term-of-service-component></term-of-service-component>
        </v-row>
      </v-container>

      <v-card-text>
        <v-checkbox
          :disabled="!can_request"
          v-model="checkbox"
          label="利用規約に同意する"
        ></v-checkbox>
      </v-card-text>

      <v-row>
        <v-col cols="2"></v-col>
        <v-col
          cols="3"
          class="my-center"
        >
          <v-card-actions>
            <v-dialog
              v-model="dialog"
              persistent
              max-width="375"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  rounded
                  color="success"
                  class="mr-4"
                  @click="openConfirmDialog"
                  :disabled="!checkbox"
                >
                  会員登録
                </v-btn>
              </template>
              <v-card>
                <v-card-title class="headline">
                  会員登録をしますか？
                </v-card-title>
                <v-card-text>
                  OKを押すとメールで認証コードが送信されます。
                </v-card-text>

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
                    @click="submit"
                  >
                    OK
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
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
              <router-link :to="{ name: 'auth.login'}">ログインはこちら</router-link>
            </v-btn>
          </v-card-actions>
        </v-col>

        <v-col cols="2"></v-col>

      </v-row>

    </v-form>

    <v-card-text align="center">SNSで会員登録</v-card-text>

    <twitter-connect-button></twitter-connect-button>

  </v-card>
</template>

<script>
import TwitterConnectButton from '../LayoutItems/TwitterConnectButton';
import TermOfServiceComponent from '../Help/Items/TermOfServiceComponent';
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
      v => /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,50}$/.test(v) || 'パスワードは半角英数字6文字以上です。',
    ],
    checkbox: false,
    show: false,
    dialog: false,
    policy_scrolled: false,
    form_valid: false,
    can_request: false,
  }),
  components: {
    TwitterConnectButton,
    TermOfServiceComponent
  },
  computed: {
    toggle() {
      const icon = this.show ? 'mdi-eye' : 'mdi-eye-off';
      const type = this.show ? 'text' : 'password';
      return {icon, type};
    },
  },
  watch: {
    email: function() {
      this.form_valid = this.$refs.form.validate();
      this.can_request = this.form_valid && this.policy_scrolled;
    },
    password: function() {
      this.form_valid = this.$refs.form.validate();
      this.can_request = this.form_valid && this.policy_scrolled;
    }
  },
  methods: {
    /**
     * 新規ユーザーの登録
     */
    submit() {
      localStorage.auth_id = this.email;
      this.dialog = false;
      this.$store.dispatch('register', {
        name: this.name,
        email: this.email,
        password: this.password,
      });
    },
    openConfirmDialog() {
      this.dialog = true;
    },
    onScroll(e) {
      if (this.isFullScrolled(e)) {
        // 一番下までスクロールした際の処理
        this.policy_scrolled = true;
          this.can_request = this.form_valid && this.policy_scrolled;
      }

    },
    isFullScrolled(e) {
      // ブラウザの設定にもよるので、完全に一番下までいっていなくても許容するための調整値
      const adjustment_value = 60
      const position_with_adjustment_value = e.target.clientHeight + e.target.scrollTop + adjustment_value
      return position_with_adjustment_value >= e.target.scrollHeight
    }
  },
};
</script>

<style scoped>
.my-center {
  display: flex;
  justify-content: center;
}
</style>
