import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useCustomerScheduleStore = defineStore('useCustomerScheduleStore', {
  actions: {
    // 👉 Fetch tele-sales-customers data

    // 👉 Add Customer
    addSchedule(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/schedule-customer', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

  },
})

