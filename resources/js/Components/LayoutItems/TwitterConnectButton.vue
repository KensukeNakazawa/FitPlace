<template>
  <v-card-actions>
    <a :href="path">
      <v-btn color="#1DA1F2" dark style="text-transform: none">
        <v-icon size="24px">
          mdi-twitter
        </v-icon>
        Twitter
      </v-btn>
    </a>
  </v-card-actions>
</template>

<script>
export default {
  props: {
    path: {
      type: String,
      default: '/socials/twitter/auth'
    },
  },
}
</script>