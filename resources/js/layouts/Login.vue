<template>
   <div class="login-page">
    <div class="login-box" bis_skin_checked="1">
      <div class="login-logo" bis_skin_checked="1">
        <a href="/login"><b>Admin</b>GPT</a>
      </div>

      <div class="card" bis_skin_checked="1">
        <div class="card-body login-card-body" bis_skin_checked="1">
          <p class="login-box-msg">Đăng nhập vào trang quản trị</p>
          <form action="/" method="post">
            <div class="input-group mb-3" bis_skin_checked="1">
              <input type="email" class="form-control" placeholder="Email" v-model="loginData.email">
              <div class="input-group-append" bis_skin_checked="1">
                <div class="input-group-text" bis_skin_checked="1">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3" bis_skin_checked="1">
              <input type="password" class="form-control" placeholder="Password" v-model="loginData.password">
              <div class="input-group-append" bis_skin_checked="1">
                <div class="input-group-text" bis_skin_checked="1">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row" bis_skin_checked="1">
              <div class="col-8" bis_skin_checked="1">
                <div class="icheck-primary" bis_skin_checked="1">
                  <input type="checkbox" id="remember" v-model="loginData.remember">
                  <label for="remember">
                    <!-- Remember Me -->
                  </label>
                </div>
              </div>

              <div class="col-4" bis_skin_checked="1">
                <button type="button" class="btn btn-primary btn-block" @click.prevent="login()">Đăng nhập</button>
              </div>

            </div>
          </form>
       
        </div>

      </div>
    </div>
  </div>
</template>
<script setup>
import axios from 'axios'
import {  ref} from "vue"
import { toast } from 'vue3-toastify';
import router from '@/router/index.js'

const loginData = ref({
  'email':'',
  'password':'',
  'remember':'',
 })
const login = () => {
  axios.post('/api/login', {
    email: loginData.value.email,
    password: loginData.value.password,
  })
    .then(response => {
      localStorage.setItem('user', JSON.stringify(response.data.user));
      localStorage.setItem('accessToken', response.data.access_token);
      router.push('/');
    }).catch(error => {
      toast.error(error.response.data.message)
    })
}
</script>