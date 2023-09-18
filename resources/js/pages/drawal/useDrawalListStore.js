import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useDrawalListStore = defineStore('DrawalListStore', {
  actions: {
    // 👉 Fetch drawals data
    fetchDrawals(params,searchValue,customer_id) { 
      params.search = searchValue
      params.customer_id = customer_id
        return axios.get('/drawals', { params }) },

    // 👉 Add Drawal
    addDrawal(userData) {
        axios.post('/drawals', userData).then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 fetch single user
    fetchDrawal(id) {
        axios.get(`/drawals/${id}`).then(response => resolve(response)).catch(error => reject(error))
    },

    // 👉 Update Department
    updateData(data) {
        axios.put(`/drawals/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 delete single department
    deleteData(id) {
        axios.delete(`/drawals/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

    reDoneData(id) {
        axios.post(`/drawals/reDone/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },
    verifyData(id) {
        axios.post(`/drawals/verify/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },


  },
})

