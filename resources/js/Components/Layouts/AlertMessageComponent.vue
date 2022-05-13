<template>
  <div
    v-show="$store.state.flash_message.message"
    class="alert-message"
  >
    <v-alert
      v-if="$store.state.flash_message.type === 'success'"
      dismissible
      elevation="24"
      type="success"
    >
      {{ $store.state.flash_message.message }}
    </v-alert>

    <v-alert
      v-else-if="$store.state.flash_message.type === 'info'"
      dismissible
      elevation="24"
      type="info"
    >
      {{ $store.state.flash_message.message }}
    </v-alert>

    <v-alert
      v-else-if="$store.state.flash_message.type === 'error'"
      dismissible
      elevation="24"
      type="error"
    >
      {{ $store.state.flash_message.message }}
    </v-alert>
  </div>

</template>

<script>
import {mapState} from 'vuex';

export default {
  computed: {
    ...mapState({
      message: 'message',
      type: 'type'
    }),
  },
};
</script>

<style>
.alert-message {
  position: fixed;
  width: 100%;
  z-index: 100;
  margin-top: 0.25em;
}
</style>