import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useExpenseListStore = defineStore('ExpenseListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchExpenses(params,searchValue) { 
      const params_merge = Object.assign( {}, params, searchValue );
      return axios.get('/expenses', { params:params_merge }) },
  },
})

