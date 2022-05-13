<template>
  <!--
    Loading の実装 vuex
    https://www.hypertextcandy.com/loading-with-vuex
    https://zukucode.com/2017/04/vuex-action-loading.html
  -->
  <div v-show="$store.state.auth.loading">
    <div class="full-view">
      <div class="loading-spacer"></div>
      <vue-loading
        type="cylon"
        color="#4169e1"
        :size="{ width: '100px', height: '100px' }"
      >
        Loading....
      </vue-loading>
    </div>
  </div>
</template>

<script>
import {VueLoading} from 'vue-loading-template';
import {mapState} from 'vuex';

export default {
  computed: {
    ...mapState({
      loading: 'loading',
    }),
  },
  components: {
    VueLoading,
  },
};
</script>

<style>
.full-view {
  width: 100%;
  height: 100%;
  background: rgba(9, 30, 66, 0.54);
  position: fixed;
  z-index: 10000000000;
  top: 0;
  /*left: 0;*/
}

.loading-spacer {
  height: 30%;
}
</style>