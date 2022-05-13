<template>

  <v-card>
    <v-card-title>パスワード変更</v-card-title>
    <v-form
      ref="form"
      v-model="valid"
      lazy-validation
    >
      <v-card-text>
        <v-text-field
          v-model="old_password"
          :rules="password_rules"
          label="現在のパスワード"
          :counter="30"
          required
          :append-icon="old_password_toggle.icon"
          :type="old_password_toggle.type"
          @click:append="old_password_show = !old_password_show"
        ></v-text-field>
      </v-card-text>

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
        <v-dialog
          v-model="dialog"
          persistent
          max-width="290"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              :disabled="!valid"
              color="success"
              class="mr-4"
              @click="openConfirmDialog"
            >
              パスワードを変更する
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">
              パスワードを変更しますか？
            </v-card-title>
            <v-card-text>
              OKを押すとパスワードを変更されます。
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

    </v-form>
  </v-card>
</template>

<script>
export default {
  mounted() {
    // this.$store.dispatch('showLoading');
  },
  data: () => ({
    valid: true,
    old_password: '',
    new_password: '',
    confirm_password: '',
    password_rules: [
      v => !!v || '必須項目です',
      v => /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,50}$/.test(v) || '半角英数字6文字以上です。',
    ],
    old_password_show: false,
    new_password_show: false,
    confirm_password_show: false,
    dialog: false,
    can_request: false,
  }),
  created() {
    this.$store.dispatch('hideLoading');
  },
  computed: {
    old_password_toggle() {
      const icon = this.old_password_show ? 'mdi-eye' : 'mdi-eye-off';
      const type = this.old_password_show ? 'text' : 'password';
      return {icon, type};
    },
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
    old_password: function() {
      this.can_request = this.$refs.form.validate();
    },
    new_password: function() {
      this.can_request = this.$refs.form.validate();
    },
    confirm_password: function() {
      this.can_request = this.$refs.form.validate();
    }
  },
  methods: {
    submit() {
      this.dialog = false;

      /** 新しいパスワードと確認用のパスワードが同じであること */
      if (this.new_password !== this.confirm_password) {
        this.$store.dispatch('setMessage', {
          message: '新しいパスワードと確認用パスワードは同じものを入力してください',
          type: 'error',
        });
        return
      }

      this.$store.dispatch('showLoading');
      axios.put('/api/me/password', {
        old_password: this.old_password,
        new_password: this.new_password,
        confirm_password: this.confirm_password
      }).then(() => {
        this.$store.dispatch('setMessage', {
          message: '更新できました！',
          type: 'success',
        });
        this.$store.dispatch('hideLoading');
      });
    },
    openConfirmDialog() {
      this.dialog = true;
    }
  }
}
</script>

<style scoped>

</style>