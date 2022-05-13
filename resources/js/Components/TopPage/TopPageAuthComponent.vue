<template>
  <div>
    <v-card-title>
      <span class="title-about-text">
        ログイン & 会員登録
      </span>
    </v-card-title>

    <v-row>

      <v-col
        cols="12"
        sm="6"
        md="6"
        lg="6"
        xl="6"
      >
        <div
          class="thumbnail-image anim-box"
          v-scroll:#scroll-target="onScroll" >
          <v-img
            src="images/top/login.png"
          >
            <template v-slot:placeholder>
              <v-row
                class="fill-height ma-0"
                align="center"
                justify="center"
              >
                <v-progress-circular
                  indeterminate
                  color="grey lighten-5"
                ></v-progress-circular>
              </v-row>
            </template>
          </v-img>
        </div>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="6"
        lg="6"
        xl="6"
      >
        <div class="title-content">
          既に登録済みの方はログインボタンをクリック
          まだ登録していない方は会員登録をクリックして、
          効率の良いトレーニングライフへの一歩を踏み出しましょう!
        </div>

        <v-btn rounded>
          <router-link :to="{ name: 'auth.login' }">
            ログイン
          </router-link>
        </v-btn>
        <v-btn rounded>
          <router-link :to="{ name: 'auth.register' }">
            会員登録
          </router-link>
        </v-btn>
      </v-col>

    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({}),
  methods: {
    onScroll() {
      console.log("Scrolled!! ")
    },
    handleScroll: function(evt, el) {
      if (window.scrollY > 50) {
        el.setAttribute("style", "opacity: 1; transform: translate3d(0, -10px, 0)")
      }
      return window.scrollY > 100;
    }
  }
}
</script>
<style>
.title-about-text {
  margin-bottom: -17.5px;
  border-bottom: ridge 2px #4c6cb3
}

.thumbnail-image {
  margin-top: 14px;
}

.title-content {
  max-width: 100%;
  margin: 2em 0;
  line-height: 2.8;
}

.anim-box{
  opacity : 0;
  visibility: hidden;
  transition: 1s;
  transform: translateY(30px);
}

.active{
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}
</style>