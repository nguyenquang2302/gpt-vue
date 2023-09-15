import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useExpenseListStore = defineStore('ExpenseListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchExpenses(params) { 
        return axios.get('/expenses',{params}) },
  },
})

