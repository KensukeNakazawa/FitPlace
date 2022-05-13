<template>
  <div>
    <v-card-title>
      <span class="title-about-text">
        FitPlaceとは
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
          <p>
            FitPlaceは筋トレを効率的に行うために、<br>
            トレーニングの予定、トレーニングの記録、トレーニングのリマインドをするサービスです。
          </p>
          <p>
            FitPlaceを使って、効率的に目標までの階段を登りましょう。
          </p>
        </div>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="6"
        lg="6"
        xl="6"
      >
        <div class="thumbnail-image">
          <v-img
            src="images/top/tobe.png"
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
  </div>
</template>

<style>
.title-about-text {
  margin-bottom: -17.5px;
  border-bottom: ridge 2px #89c3eb
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