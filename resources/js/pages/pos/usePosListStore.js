import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const usePosListStore = defineStore('PosListStore', {
  actions: {
    // 👉 Fetch pos data
    fetchPos(params,searchValue) { 
      const params_merge = Object.assign( {}, params, searchValue );
        return axios.get('/pos', { params:params_merge }) },

    fetchAllPos(params) { 
      return axios.get('/pos/search', { params }) },

    // 👉 Add Customer
    addCustomer(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/pos', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchPosId(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/pos/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.put(`/pos/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 delete single department
    deleteData(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/pos/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

  },
})

