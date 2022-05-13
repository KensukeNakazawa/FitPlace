<template>
  <div>
    <v-footer
      absolute
      padless
      class="footer-item"
    >
      <v-col
        class="text-left"
        cols="12"
      >
        <strong class="">ご意見、ご感想はDMまたはメールにて！</strong>
        <span v-if="!$store.state.auth.is_login">
          <a href="https://storyset.com/production" style="font-size: 1px">Production illustrations by Storyset</a>
        </span>
      </v-col>

      <v-col
        class="text-left"
        cols="12"
      >
        <a href="https://twitter.com/kensuke__19">
          <v-btn>
            <v-icon size="24px">
              mdi-twitter
            </v-icon>
          </v-btn>
        </a>

        <a href="mailto:inform.fitplace@gmail.com?subject=問い合わせ&body=ご意見ご感想をお願いします。">
          <v-btn>
            <v-icon size="24px">
              mdi-email
            </v-icon>
          </v-btn>
        </a>
      </v-col>

      <v-col
        class="text-center"
        cols="12"
      >
        <v-icon>mdi-copyright</v-icon>
        Copyright
        {{ new Date().getFullYear() }} — FitPlace
      </v-col>
    </v-footer>
  </div>
</template>

<script>

</script>

<style scoped>
.footer-item {
  margin-top: 2em;
}
</style>