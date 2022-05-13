
const state = {
  loading: false
}

const mutations = {
  setLoading(state, payload) {
    state.loading = payload
  }
}

const actions = {
  showLoading({ commit }) {
    commit('setLoading', true)
  },
  hideLoading( { commit }) {
    commit("setLoading", false)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
