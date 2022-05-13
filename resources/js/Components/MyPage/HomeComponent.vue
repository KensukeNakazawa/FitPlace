<template>
<div>
  <v-banner><h3>マイページ</h3></v-banner>
  <v-card v-if="user && auth">
    <v-card-title>{{ user.name }}</v-card-title>

    <v-card-subtitle>{{ auth.email }}</v-card-subtitle>
    <v-card-text></v-card-text>

    <v-row>
      <v-col cols="7">
        <v-card-actions>
          <router-link :to="{ name: 'my_page.password' }">
            <v-btn>
              パスワードを変更
            </v-btn>
          </router-link>
        </v-card-actions>
      </v-col>

      <v-col cols="3">
        <v-card-actions>
          <router-link :to="{ name: 'my_page.notify_setting' }">
            <v-btn>
              通知設定
            </v-btn>
          </router-link>
        </v-card-actions>
      </v-col>

      <v-space></v-space>

    </v-row>

    <v-card-subtitle align="center">SNSと連携</v-card-subtitle>

    <twitter-connect-button :path="twitter_connect_endpoint"></twitter-connect-button>
  </v-card>
</div>
</template>

<script>

import TwitterConnectButton from '../LayoutItems/TwitterConnectButton';

export default {
  components: {TwitterConnectButton},
  data: () => ({
    user: null,
    auth: null,
    twitter_connect_endpoint: null,
  }),
  component: {
    TwitterConnectButton
  },
  mounted() {
    this.$store.dispatch('showLoading');
  },
  created() {
    this.twitter_connect_endpoint = '/socials/twitter/connect?token=' + this.$store.getters.getToken;
    axios.get('/api/me').then((res) => {
      this.user = res.data;
      this.$store.dispatch('hideLoading');
    });
    axios.get('/api/me/auth').then((res) => {
      this.auth = res.data;
    });
  }
}
</script>

<style scoped>

</style>