import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useTransactionListStore = defineStore('TransactionListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchTransactions(params) { 
        return axios.get('/transactions',{params}) },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.post(`/bill-return`,data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },

})

