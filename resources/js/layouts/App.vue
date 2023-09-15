<template>
  <div v-if =userLogin>
    <div class="wrapper">
      <!-- Navbar -->
      <Nav></Nav>
      <Sidebar></Sidebar>
    </div>
    <div >
      <div >
        <router-view></router-view>
      </div>
    </div>
  </div>
  <div v-else>
    <div class="login-page">
    <router-view></router-view>
  </div>
  </div>
</template>
<script setup>
import Nav  from './Nav.vue'
import Sidebar  from './Sidebar.vue'
const userLogin  = JSON.parse(localStorage.getItem('user'))
</script>