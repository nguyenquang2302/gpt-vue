import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useTeleSalesCustomerListStore = defineStore('useTeleSalesCustomerListStore', {
  actions: {
    // 👉 Fetch tele-sales-customers data
    fetchCustomers(params,searchValue) { 
      const params_merge = Object.assign( {}, params, searchValue );
        return axios.get('/tele-sales-customers', { params:params_merge }) },

    fetchAllCustomers(params) { 
      return axios.get('/tele-sales-customers/search', { params }) },

    // 👉 Add Customer
    addCustomer(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/tele-sales-customers', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchCustomer(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/tele-sales-customers/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.put(`/tele-sales-customers/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 delete single department
    deleteData(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/tele-sales-customers/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

  },
})

