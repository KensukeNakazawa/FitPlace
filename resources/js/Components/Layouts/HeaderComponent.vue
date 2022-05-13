<template>
  <div>
    <div v-if="$store.state.auth.is_login">
      <v-app-bar clipped-right>
        <v-app-bar-nav-icon
          @click.stop="drawer = !drawer"
        >
        </v-app-bar-nav-icon>
        <v-toolbar-title v-on:click="moveLink('home')">
          FitPlace
        </v-toolbar-title>
      </v-app-bar>
    </div>
    <div v-else>
      <v-app-bar>
        <v-toolbar-title v-on:click="moveLink('top_page')">
          FitPlace
        </v-toolbar-title>
      </v-app-bar>
    </div>

    <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
      overlay-color="#bbc8e6"
    >
      <v-list-item-group
        v-model="group"
        active-class="deep-purple--text text--accent-4"
      >
        <v-list-item>
          <v-list-item-title v-on:click="moveLink('home')">
            ホーム
          </v-list-item-title>
        </v-list-item>

        <v-list-item>
          <v-list-item-title v-on:click="moveLink('exercise_types.home')">
            トレーニング種目
          </v-list-item-title>
        </v-list-item>

        <v-list-item>
          <v-list-item-title v-on:click="moveLink('my_page.home')">
            マイページ
          </v-list-item-title>
        </v-list-item>

        <v-list-item>
          <v-list-item-title v-on:click="moveLink('help.main')">
            ヘルプ
          </v-list-item-title>
        </v-list-item>

        <v-list-item>
          <v-list-item-title v-on:click="logout">
            ログアウト
          </v-list-item-title>
        </v-list-item>

      </v-list-item-group>
    </v-navigation-drawer>
  </div>
</template>

<script>
import router from '../../router';

export default {
  data: () => (
    {
      drawer: null,
      group: null,
    }
  ),
  watch: {
    group() {
      this.drawer = false;
    },
  },
  methods: {
    logout() {
      this.$store.dispatch('logout');
    },
    moveLink(move_to) {
      router.push({name: move_to});
    }
  },
};
</script>