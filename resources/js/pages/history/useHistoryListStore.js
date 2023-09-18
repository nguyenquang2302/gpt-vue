import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useHistoryListStore = defineStore('HistoryListStore', {
  actions: {
    // 👉 Fetch customers data
    fetchHistorys(params,searchValue) { 
      	
      const params_merge = Object.assign( {}, params, searchValue );
        return axios.get('/history', { params: params_merge }) },

    // 👉 fetch single user
    fetchHistory(id) {
        axios.get(`/history/${id}`).then(response => resolve(response)).catch(error => reject(error))
    },

    // 👉 Update Department
    updateData(data) {
        axios.put(`/history/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

  },
})

