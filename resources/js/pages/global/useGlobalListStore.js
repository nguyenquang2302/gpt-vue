import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useGlobalListStore = defineStore('GlobalListStore', {
  actions: {
    // 👉 Fetch  data
    fetchGlobals() { 
      return axios.get('/globals')
    },
    fetchGlobalDetails(params) { 
      return axios.get('/global-details',{params})
    },
  },
})

