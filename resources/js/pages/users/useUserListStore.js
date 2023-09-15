import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useUserListStore = defineStore('UserListStore', {
  actions: {
    // 👉 Fetch users data
    fetchUsers(params) { 
        return axios.get('/users', { params }) },

    // 👉 Add User
    addUser(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/users', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchUser(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Update Department
    updateData(data) {
      return new Promise((resolve, reject) => {
        axios.put(`/users/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 delete single department
    deleteData(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/users/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    changePass(data) {
      return new Promise((resolve, reject) => {
        axios.patch(`/users/password/change/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch logs data
    fetchLogs(params) { return axios.get('/activeLogs', { params }) }
  },
})

