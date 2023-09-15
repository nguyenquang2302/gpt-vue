import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useDashboardPosListStore = defineStore('DashboardPosListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchDashboardPos(params) { 
        return axios.get('/dasboard-pos',{ params}) },
  },
})

