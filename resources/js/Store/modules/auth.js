import router from '../../router';
import store from '../index';

const state = {
  token: '',
  auth_id: '',
  is_login: false,
  loading: false,
  line_notify_dialog: false,
}

const mutations = {
  login(state, payload) {
    state.token = payload.token;
    state.auth_id = payload.auth_id
    state.is_login = true;
  },
  logout(state) {
    state.token = null;
    state.auth_id = null;
    state.is_login = false;
  },
  authCode(state, payload) {
    state.token = payload.token;
    state.auth_id = payload.auth_id;
  },

  setLoading(state, payload) {
    state.loading = payload
  },

  setLogin(state, payload) {
    state.is_login = payload;
  },

  setLineNotifyDialog(state, payload) {
    state.line_notify_dialog = payload
  }
};

const getters = {
  isLogin(state) {
    return !!state.token;
  },
  getToken(state) {
    return state.token;
  },

};

const actions = {
  /**
   * ログインを行い、永続化する
   * @param commit
   * @param payload
   */
  login({ commit }, payload) {
    commit('setLoading', true)
    axios.post('/api/auth/login', {
      email: payload.email,
      password: payload.password
    }).then(res => {
      const token = res.data.access_token;
      const email = payload.email;

      commit('login', {token: token, auth_id: email});
      const message = 'ログインしました！';
      store.dispatch('setMessage', {
        message: message,
        type: 'success',
      });
      router.push({name: 'home'});
    });
  },

  /**
   * ログアウトする
   * @param commit
   */
  logout ({ commit }) {
    commit('setLoading', true);
    axios.post('/api/auth/logout').then(res => {
      commit('logout');
      router.push({path: '/auth/login'});
    });
  },

  /**
   * @param commit
   * @param payload
   */
  register({ commit }, payload) {
    commit('setLoading', true)
    axios.post('/api/auth/register', {
      name: payload.name,
      email: payload.email,
      password: payload.password,
    }).then((res) => {
      router.push({name: 'auth.auth_code'});
      // commit('alert/setAlert', {'message': 'ユーザー登録しました'}, {root: true});
    });
  },

  /**
   * @param commit
   * @param payload
   */
  authCode({ commit }, payload) {
    commit('setLoading', true)
    axios.post('/api/auth/authorize_code', {
      auth_code: payload.auth_code,
      auth_id: payload.auth_id,
    }).then((res) => {
      const token = res.data.access_token;

      commit('authCode', {token: token, auth_id: localStorage.auth_id});

      localStorage.auth_id = null;
      router.push({name: 'users.create'});
    });
  },

  createUser({ commit }, payload) {
    commit("setLoading", true);
    axios.post('/api/users/store', {
      name: payload.name,
      birth_day: payload.birth_day,
    }).then(() => {
      commit("setLineNotifyDialog", true);
      commit('setLogin', true)
      router.push({name: 'home'});
    });
  },

  showLoading({ commit }) {
    commit('setLoading', true)
  },
  hideLoading( { commit }) {
    commit("setLoading", false)
  },
  openNoticeLineNotifyDialog( { commit }) {
    commit("setLineNotifyDialog", true);
  },
  closeNoticeLineNotifyDialog( { commit }) {
    commit("setLineNotifyDialog", false);
  }

};

export default {
  namespaces: true,
  state,
  mutations,
  getters,
  actions
};
