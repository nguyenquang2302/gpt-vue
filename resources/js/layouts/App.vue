<template>
    <router-view></router-view>
</template>
<script setup>
import Nav  from './Nav.vue'
import Sidebar  from './Sidebar.vue'
const userLogin  = JSON.parse(localStorage.getItem('user'))

</script>