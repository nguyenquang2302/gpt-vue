import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const usePartnerListStore = defineStore('usePartnerListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchTransactions(params) { 
        return axios.get('/transaction-partner',{params}) },
  },
})

