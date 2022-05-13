import Vue from 'vue';
import Vuex from 'vuex';

// stateの永続化
import createPersistedState from 'vuex-persistedstate';

import auth from './modules/auth';
import flash_message from './modules/flash_message';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
    flash_message,
  },
  /**
   * @see https://www.webopixel.net/javascript/1463.html
   */
  strict: true,
  plugins: [createPersistedState({
    /**　sessionStorageのキー */
    key: 'connection',
    /** 保存するstate */
    paths: [
      'auth.token',
      'auth.auth_id',
      'auth.is_login',
    ],
    /** ストレージの種類 */
    storage: window.localStorage
  })]
});