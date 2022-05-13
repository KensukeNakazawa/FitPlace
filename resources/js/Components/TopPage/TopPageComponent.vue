<template>
<v-container>

  <top-page-title-component></top-page-title-component>

  <top-page-about-us-component></top-page-about-us-component>

  <top-page-auth-component></top-page-auth-component>

  <top-page-service-function-component></top-page-service-function-component>

  <top-page-auth-component></top-page-auth-component>

</v-container>
</template>

<script>
import TopPageTitleComponent from './TopPageTitleComponent'
import TopPageAboutUsComponent from './TopPageAboutUsComponent'
import TopPageServiceFunctionComponent from './TopPageServiceFunctionComponent'
import TopPageAuthComponent from './TopPageAuthComponent'
export default {
  components: {
    TopPageAuthComponent,
    TopPageTitleComponent,
    TopPageAboutUsComponent,
    TopPageServiceFunctionComponent
  }
}
</script>

<style scoped>
.item-overview {
  margin-top: 0.5em;
}

.sub-title {
  font-size: 1.5em;
}

.sub-item-title {
  font-size: 1.25em;
}
.my-center {
  display: flex;
  justify-content: center;
}
</style>