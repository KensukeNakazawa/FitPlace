import Vue from 'vue'
import Axios from 'axios'
import errorHandler from './errorHandler';
import store from '../Store';

const http = Axios.create({
  // for cors
  withCredentials: true
})

http.interceptors.response.use(function (response) {

}, function (error) {
  // 認証エラー時の処理
  const status_code = error.response.status;
  const error_messages = response.data.errors;
  const alert_message = errorHandler(status_code, error_messages);
  store.dispatch('setMessage', {
    message: alert_message,
    type: 'error',
  });
  return Promise.reject(error)
})

export default http