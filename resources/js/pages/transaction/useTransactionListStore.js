import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useTransactionListStore = defineStore('TransactionListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchTransactions(params) { 
        return axios.get('/transactions',{params}) },
  },
})

