const state = {
  message: '',
  type: 'success'
};

const mutations = {
  setMessage (state, {message, type}) {
    state.message = message
    state.type = type || 'success'
  }
};

const actions = {
  setMessage({ commit }, payload) {
    const message = payload.message;
    const type = payload.type;
    commit('setMessage', {message, type});

    const timeout = 3000;
    setTimeout(() => commit('setMessage', {message: '', type: 'empty'}), timeout);
  },
};

export default {
  namespaces: true,
  state,
  mutations,
  actions
};
