<template>
  <div>
    <v-card-title>
      <span class="title-about-text">
        FitPlaceへようこそ
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
        <div class="thumbnail-image">
          <v-img
            src="images/top/squat.png"
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
          FitPlaceは予定を立てず筋トレをしていて、ダラダラトレーニングしてしまったことがある開発者が、
          もっと効率的に筋トレをしたい...<br>
          そんな思いから開発したサービスです。
        </div>
      </v-col>

    </v-row>
  </div>
</template>

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

.anim-box.fadeup.is-animated {
  animation: fadeup 1s cubic-bezier(0.33, 1, 0.68, 1) 1 forwards;
}

@keyframes fadeup {
  0% {
    transform: translateY(30px);
    opacity: 0;
  }
  80% {
    opacity: 1;
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

</style>