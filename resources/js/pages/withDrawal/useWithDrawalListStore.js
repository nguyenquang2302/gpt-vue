import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useWithDrawalListStore = defineStore('WithDrawalListStore', {
  actions: {
    // 👉 Fetch withdrawals data
    fetchWithDrawals(params,searchValue,customer_id) { 
      params.search = searchValue
      params.customer_id = customer_id
        return axios.get('/withdrawals', { params }) },

    // 👉 Add WithDrawal
    addWithDrawal(userData) {
        axios.post('/withdrawals', userData).then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 fetch single user
    fetchWithDrawal(id) {
        axios.get(`/withdrawals/${id}`).then(response => resolve(response)).catch(error => reject(error))
    },

    // 👉 Update Department
    updateData(data) {
        axios.put(`/withdrawals/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 delete single department
    deleteData(id) {
        axios.delete(`/withdrawals/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

    reDoneData(id) {
        axios.post(`/withdrawals/reDone/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },
    verifyData(id) {
        axios.post(`/withdrawals/verify/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },


  },
})

