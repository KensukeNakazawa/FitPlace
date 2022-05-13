<template>
  <v-app
    class="main-background wrap-original"
  >

    <v-main>

      <header-component></header-component>
      <loading-component></loading-component>
      <alert-message-component></alert-message-component>
      <line-notify-dialog-component></line-notify-dialog-component>
      <v-container>
        <div class="router-item">
          <router-view></router-view>
        </div>
      </v-container>
      <footer-component></footer-component>

    </v-main>

  </v-app>
</template>

<script>
import AlertMessageComponent from './Components/Layouts/AlertMessageComponent';
import HeaderComponent from './Components/Layouts/HeaderComponent';
import LineNotifyDialogComponent from './Components/Layouts/LineNotifyDialogComponent';
import LoadingComponent from './Components/Layouts/LoadingComponent';
import FooterComponent from './Components/Layouts/FooterComponent';

import router from './router';

export default {
  components: {
    AlertMessageComponent,
    HeaderComponent,
    LineNotifyDialogComponent,
    LoadingComponent,
    FooterComponent,
    router,
  },
};
</script>

<style scoped>
.main-background {
  background-color: #b2ebf2
  /*#*/
  /*// #eaf4fc*/
}
.wrap-original {
  overflow: hidden;
}
.router-item {
  margin-bottom: 9.5em;
}

</style>