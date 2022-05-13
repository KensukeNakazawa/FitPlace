<template>
  <v-dialog
    v-model="$store.state.auth.line_notify_dialog"
    persistent
  >
    <v-card>
      <v-card-title class="headline">
        LINE Notifyと連携してもっと効率良く！
      </v-card-title>
      <v-card-text>
        LINE Notifyと連携することで、各種通知を受け取れるようになります。<br>
        <v-list>
          <v-list-item>
            トレーニング予定がある日に、指定した時間で通知
          </v-list-item>
          <v-list-item>
            週に1回その週のトレーニングボリュームとセット数を通知
          </v-list-item>
        </v-list>

      </v-card-text>

      <v-row class="notify-content">
        <v-spacer></v-spacer>
        <v-col
          cols="6"
          sm="6"
          md="4"
          lg="4"
          xl="4"
        >
          <v-img src="/images/line_notify/plan_notify.png"></v-img>
        </v-col>

        <v-col
          cols="6"
          sm="6"
          md="4"
          lg="4"
          xl="4"
        >
          <v-img src="/images/line_notify/summary_notify.png"></v-img>
        </v-col>

        <v-spacer></v-spacer>
      </v-row>
      <v-card-actions>
        <v-btn
          color="green darken-1"
          dark
          @click="moveToLink('my_page.notify_setting')"
        >
          連携する
        </v-btn>
        <v-btn
          color="green darken-1"
          text
          @click="checkLineNotify"
        >
          今回は連携しない
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import moment from 'moment';
import router from '../../router';
import {mapState} from 'vuex';
export default {
  computed: {
    ...mapState({
      line_notify_dialog: 'line_notify_dialog',
    }),
  },
  created() {
    axios.get('/api/me/line_notify_access_token')
    .then((res) => {
      const line_notify = res.data.line_notify;

      if (!line_notify) {
        this.$store.dispatch('openNoticeLineNotifyDialog');
        return
      }

      const target_date = moment().add(-14, 'days').format('YYYY-MM-DD');

      if (line_notify.access_token) {
        return
      }

      if (new Date(target_date) > new Date(line_notify.check_at)) {
        this.$store.dispatch('openNoticeLineNotifyDialog');
      }
    });
  },
  methods: {
    moveToLink(move_to) {
      this.checkLineNotify();
      router.push({
        name: move_to,
      });
    },
    checkLineNotify()
    {
      this.$store.dispatch('closeNoticeLineNotifyDialog');
      axios.put('/api/me/check_line_notify')
      .then(() => {

      });
    }
  }
}
</script>

<style scoped>
.notify-content {
  margin-left: 0.5px;
  margin-right: 0.5px;
  margin-bottom: 0.25px;
}
</style>