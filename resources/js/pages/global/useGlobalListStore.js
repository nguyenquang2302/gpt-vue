import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useGlobalListStore = defineStore('GlobalListStore', {
  actions: {
    // 👉 Fetch  data
    fetchGlobals() { 
      return axios.get('/globals')
    },

    fetchGlobalDetails(params) {
      return new Promise((resolve, reject) => {
        axios.get('/global-details',{ params}).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})

