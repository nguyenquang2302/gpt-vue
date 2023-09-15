import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useCustomerListStore = defineStore('CustomerListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchCustomers(params,searchValue) { 
      const params_merge = Object.assign( {}, params, searchValue );
        return axios.get('/customers', { params:params_merge }) },

    fetchAllCustomers(params) { 
      return axios.get('/customers/search', { params }) },

    // 👉 Add Customer
    addCustomer(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/customers', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchCustomer(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/customers/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.put(`/customers/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 delete single department
    deleteData(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/customers/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

  },
})

