<template>
  <div>
    <v-card-title>
      <span class="title-about-text">
        FitPlaceでできること
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
        <div class="title-content">
          <ol>
            <li>筋トレの予定を組むこと</li>

            <li>筋トレの記録をすること</li>

            <li>筋トレの予定をリマインドすること</li>

            <li>筋トレを振り返ること</li>
          </ol>
          筋トレは予定を立てて、それを着実にこなすことが大事だと思います。<br>
          FitPlace内で予定を立てて、実行したトレーニングを記録しましょう。<br>

        </div>
      </v-col>
      <v-col
        cols="12"
        sm="6"
        md="6"
        lg="6"
        xl="6"
      >
        <div>
          <v-img
            src="images/top/push_up.png"
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

    </v-row>

    <v-row>
      <v-col
        cols="12"
        sm="6"
        md="6"
        lg="6"
        xl="6"
      >
        <div>
          <v-img
            src="images/top/notify.png"
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
          FitPlaceはLineと連携することで通知を受け取ることができます。<br>
          指定した時間に予定のトレーニングの通知、<br>
          週に一回、その週のトレーニングのサマリーを通知で受け取ることができます。
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
</style>