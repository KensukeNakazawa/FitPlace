<template>

  <v-card>
    <v-card-title>通知設定</v-card-title>

    <v-card-subtitle>LineNotifyと連携</v-card-subtitle>
    <v-card-text>
      LineNotifyと連携することで、各種通知を受け取ることができます。
    </v-card-text>

    <span v-if="line_notify_connected">
      <v-dialog
        v-model="line_notify_disconnect_dialog"
        persistent
        max-width="290"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-card-text>
            <v-btn
              color="#cccccc"
              @click="openLineNotifyDisconnectConfirmDialog"
            >
              LINE Notifyとの連携を解除
            </v-btn>
          </v-card-text>
        </template>
        <v-card>
          <v-card-title class="headline">
             LINE Notifyとの連携を解除しますか？
          </v-card-title>
          <v-card-text>
            OKを押すと LINE Notifyとの連携を解除します。
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="green darken-1"
              text
              @click="line_notify_disconnect_dialog=false"
            >
              Cancel
            </v-btn>
            <v-btn
              color="green darken-1"
              text
              @click="disconnect"
            >
              OK
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </span>

    <span v-else>
      <v-dialog
        v-model="line_notify_connect_dialog"
        persistent
        max-width="290"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-card-text>
            <v-btn dark color="#1bb71f" @click="openLineNotifyConnectConfirmDialog">LINE Notifyと連携</v-btn>
          </v-card-text>
        </template>
        <v-card>
          <v-card-title class="headline">
            LineNotifyと連携しますか？
          </v-card-title>
          <v-card-text>
            OKを押すとLineNotifyのページに遷移します。
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="green darken-1"
              text
              @click="line_notify_connect_dialog=false"
            >
              Cancel
            </v-btn>
            <a :href="line_notify_connect_endpoint">
              <v-btn
                color="green darken-1"
                text
              >
                OK
              </v-btn>
            </a>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </span>

    <span v-if="line_notify_connected">
      <v-card-subtitle>通知希望時間の設定</v-card-subtitle>
      <v-card-subtitle>
        設定した時間に、その日の予定のトレーニングを通知することができます(予定のトレーニングが無い場合は通知されません)。
      </v-card-subtitle>
      <v-form>
        <v-card-text>
          <v-select
            v-model="notify_setting_ids"
            :items="notify_times"
            :item-text="notify_time => notify_time.time + '時'"
            item-value="id"
            attach
            chips
            label="通知希望時間"
            multiple
          ></v-select>
        </v-card-text>
      </v-form>

      <v-card-actions>
        <v-btn
          rounded
          color="success"
          class="mr-4"
          @click="updateNotifySetting"
        >
          更新する
        </v-btn>
      </v-card-actions>
    </span>
    <span v-else>
      <v-card-text>
        <ol>
          <li>LINE Notifyと連携ボタンをクリックした後、OKをクリック(LINE Notifyのページに移動します)</li>
          <li>1:1で LINE Notifyから通知を受け取るを選択</li>
          <li>同意して連携するボタンをクリック</li>
        </ol>
      </v-card-text>
      <v-row class="line-notify-setting">
        <v-col
          cols="4"
          sm="4"
          md="4"
          lg="4"
          xl="4"
        >
          <v-img src="/images/line_notify/line_notify_connect_1.png"></v-img>
        </v-col>
         <v-col
           cols="4"
           sm="4"
           md="4"
           lg="4"
           xl="4"
         >
           <v-img src="/images/line_notify/line_notify_connect_2.png"></v-img>
         </v-col>
         <v-col
           cols="4"
           sm="4"
           md="4"
           lg="4"
           xl="4"
         >
           <v-img src="/images/line_notify/line_notify_connect_3.png"></v-img>
          </v-col>
      </v-row>
    </span>
  </v-card>

</template>

<script>
export default {
  mounted() {
    this.$store.dispatch('showLoading');
  },
  data: () => ({
    line_notify_connect_endpoint: null,
    notify_times: [],
    notify_setting_ids: [],
    line_notify_connected: false,
    line_notify_connect_dialog: false,
    line_notify_disconnect_dialog: false
  }),
  created() {
    this.line_notify_connect_endpoint = '/socials/line_notify/connect?token=' + this.$store.getters.getToken;

    axios.get('/api/me/line_notify_access_token')
    .then((res) => {
      const line_notify = res.data.line_notify;
      if (line_notify && line_notify.access_token) {
        this.line_notify_connected = true;
      }
    });

    axios.get('/api/me/notify_times')
    .then((res) => {
      this.notify_times = res.data.notify_times;

      let notify_settings = res.data.notify_settings;
      for (const index in notify_settings) {
        this.notify_setting_ids.push(notify_settings[index]['notify_time_id']);
      }

      if (this.$route.query['line_notify_message']) {
        this.$store.dispatch('setMessage',{
          message:this.$route.query['line_notify_message'],
          type: 'success'
        });
        this.$router.push({query: ''});
      }

    }).catch((error) => {
    }).finally(() => {
      this.$store.dispatch('hideLoading');
    });

  },
  methods: {
    /**
     * LineNotifyとの連携を解除する
     */
    disconnect() {
      this.line_notify_connected = false;
      this.$store.dispatch('showLoading');
      axios.put('/socials/line_notify/disconnect', {
        'token': this.$store.getters.getToken
      }).then(() => {
        this.$store.dispatch('setMessage', {
          message: '連携を解除しました！',
          type: 'success',
        });
        this.$store.dispatch('hideLoading');
      });
    },
    /**
     * 通知希望時間を更新する
     */
    updateNotifySetting() {
      this.$store.dispatch('showLoading');

      axios.put('/api/me/notify_times', {
        'notify_setting_ids': this.notify_setting_ids
      }).then(() => {
        this.$store.dispatch('setMessage', {
          message: '更新できました！',
          type: 'success',
        });
      }).catch(() => {
        this.$store.dispatch('setMessage', {
          message: '更新できませんでした連携を解除できませんでした。',
          type: 'error',
        });
      }).finally(() => {
        this.$store.dispatch('hideLoading');
      });
    },
    openLineNotifyConnectConfirmDialog() {
      this.line_notify_connect_dialog = true;
    },
    openLineNotifyDisconnectConfirmDialog() {
      this.line_notify_disconnect_dialog = true;
    }
  }
}
</script>

<style scoped>
.line-notify-setting {
  margin-left: 0.5px;
  margin-right: 0.5px;
  margin-bottom: 1px;
}

</style>