import { defineStore } from 'pinia'
import axios from 'axios'

export const useBranchListStore = defineStore('BranchListStore', {
  actions: {
    // 👉 Fetch branchs data
    fetchBranchs(params) { 
        return axios.get('/api/branch', { params }) },
    fetchAllBranchs() { 
          return axios.get('/api/branchs/lists') },

    // 👉 Add Branch
    addBranch(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/branchs', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchBranch(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/branchs/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.put(`/branchs/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 delete single department
    deleteData(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/branchs/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch logs data
    fetchLogs(params) { return axios.get('/activeLogs', { params }) }
  },
})

