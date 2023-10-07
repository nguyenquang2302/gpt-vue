import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useDashboardPosListStore = defineStore('DashboardPosListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchDashboardPos(params) {
      return new Promise((resolve, reject) => {
        axios.get('/dasboard-pos',{ params}).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})

