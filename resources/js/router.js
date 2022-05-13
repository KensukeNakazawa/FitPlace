
import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';

import store from './Store/index';

import TopPageComponent from './Components/TopPage/TopPageComponent';

import HomeComponent from './Components/Home/HomeComponent';

import LoginComponent from './Components/Auths/LoginComponent';
import PasswordResetComponent from './Components/Auths/PasswordResetComponent';
import PasswordResetMailComponent from './Components/Auths/PasswordResetMailComponent';
import RegisterComponent from './Components/Auths/RegisterComponent';
import AuthCodeComponent from './Components/Auths/AuthCodeComponent';

import CreateComponent from './Components/Users/CreateComponent';

import ExercisePlanHomeComponent from './Components/ExercisePlans/HomeComponent';
import AddExercisePlanComponent from './Components/ExercisePlans/AddComponent';
import EditExercisePlanComponent from './Components/ExercisePlans/EditComponent';

import ExerciseHomeComponent from './Components/Exercises/HomeComponent';
import AddExerciseComponent from './Components/Exercises/AddComponent';
import EditExerciseComponent from './Components/Exercises/EditComponent';

import ExerciseTypeHomeComponent from './Components/ExerciseTypes/HomeComponent';
import PastExerciseComponent from './Components/ExerciseTypes/PastExerciseComponent';

import MyPageHomeComponent from './Components/MyPage/HomeComponent';

import HelpMainComponent from './Components/Help/MainComponent';
import PasswordChangeComponent from './Components/MyPage/PasswordChangeComponent';
import NotifySettingComponent from './Components/MyPage/NotifySettingComponent';

import ExerciseGraphComponent from './Components/Graphs/ExerciseGraphComponent';

Vue.use(VueRouter);

const router = new VueRouter({
  mode: 'history',
  scrollBehavior (to, from, savedPosition) {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/',
      component: TopPageComponent,
      name: 'top_page'
    },
    {
      path: '/home',
      component: HomeComponent,
      name: 'home',
      meta: { requiresAuth: true }
    },
    {
      path: '/auth/login',
      component: LoginComponent,
      name: 'auth.login',
      meta: { notAuth: true}
    },
    {
      path: '/auth/register',
      component: RegisterComponent,
      name: 'auth.register',
      meta: { notAuth: true}
    },
    {
      path: '/auth/auth_code',
      component: AuthCodeComponent,
      name: 'auth.auth_code',
    },
    {
      path: '/auth/password_reset/send_mail',
      component: PasswordResetMailComponent,
      name: 'auth.password_reset.send_mail',
      meta: { notAuth: true }
    },
    {
      path: '/auth/password_reset/reset',
      component: PasswordResetComponent,
      name: 'auth.password_reset.reset',
      meta: { notAuth: true }
    },
    {
      path: '/users/create',
      component: CreateComponent,
      name: 'users.create',
    },
    {
      path: '/exercise_plans/exercises',
      component: ExercisePlanHomeComponent,
      name: 'exercise_plans.home',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercise_plans/exercises/add/:exercise_type_id',
      component: AddExercisePlanComponent,
      name: 'exercise_plans.add',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercise_plans/:plan_exercise_id',
      component: EditExercisePlanComponent,
      name: 'exercise_plans.edit',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercises/home',
      component: ExerciseHomeComponent,
      name: 'exercises.home',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercises/add/:exercise_type_id',
      component: AddExerciseComponent,
      name: 'exercises.add',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercises/edit/:exercise_id',
      component: EditExerciseComponent,
      name: 'exercises.edit',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercise_types',
      component: ExerciseTypeHomeComponent,
      name: 'exercise_types.home',
      meta: { requiresAuth: true }
    },
    {
      path: '/exercise_types/past_exercises/:exercise_type_id',
      component: PastExerciseComponent,
      name: 'exercise_types.past_exercises',
      meta: { requiresAuth: true }
    },
    {
      path: '/my_page/home',
      component: MyPageHomeComponent,
      name: 'my_page.home',
      meta: { requiresAuth: true }
    },
    {
      path: '/my_page/password',
      component: PasswordChangeComponent,
      name: 'my_page.password',
      meta: { requiresAuth: true }
    },
    {
      path: '/my_page/notify_setting',
      component: NotifySettingComponent,
      name: 'my_page.notify_setting',
      meta: { requiresAuth: true }
    },
    {
      path: '/graphs/exercises',
      component: ExerciseGraphComponent,
      name: 'graphs.exercises',
      meta: { requiresAuth: true }
    },
    {
      path: '/help',
      component: HelpMainComponent,
      name: 'help.main'
    }
  ]
});

/**
 * 認証済みのユーザーかをチェックし、済みでなければ、ログインページに飛ばす
 */
router.beforeEach((to, from, next) => {
  // store.commit('alert/setAlert', {'message': ''});
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // ログインしていなかったらログインページにリダイレクト
    const localStorageData = JSON.parse(localStorage.getItem('connection'));
    if (!localStorageData.auth.token) {
      next({
        path: '/auth/login',
        query: { redirect: to.fullPath}
      });
    } else {
      axios.get('/api/me', {}).then((res) => {
        const user = res.data;
      }).catch((error) => {
        const response = error.response;
        store.dispatch('setMessage', {
          message: response.data.message,
          type: 'error',
        });
        store.dispatch('logout');
      });
      next();
    }
  } else if (to.matched.some(record => record.meta.notAuth)) {
    axios.get('/api/auth/no_login', {}).then((res) => {
      const response = res.data;
      /** ログインしていない場合、ログインしていればホームにリダイレクト */
      if (response.code === '10') {
        next();
      } else if (response.code === '20') {
        next({
          path: '/home',
        });
      }
    }).catch((error) => {
      next();
    });

  } else {
    next();
  }
});


export default router;
