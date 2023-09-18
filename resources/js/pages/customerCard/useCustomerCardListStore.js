import { defineStore } from 'pinia'
import axios from '@/plugins/axios'


export const useCustomerCardListStore = defineStore('CustomerCardListStore', {
  actions: {
    // 👉 Fetch customer-cards data
    fetchCustomerCards(params,searchValue,customer_id) { 
      params.search = searchValue
      params.customer_id = customer_id
        return axios.get('/customer-cards', { params }) },
    
    fetchAllCustomerCards(params) { 
      return axios.get('/customer-cards/search', { params }) },
    

    // 👉 Add CustomerCard
    addCustomerCard(userData) {
        axios.post('/customer-cards', userData).then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 fetch single user
    fetchCustomerCard(id) {
        axios.get(`/customer-cards/${id}`).then(response => resolve(response)).catch(error => reject(error))
    },

    // 👉 Update Department
    updateData(data) {
        axios.put(`/customer-cards/${data.id}`, data)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

    // 👉 delete single department
    deleteData(id) {
        axios.delete(`/customer-cards/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
    },

  },
})

