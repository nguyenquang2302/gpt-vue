import axios from '@axios'
import { defineStore } from 'pinia';

export const useGlobalStore = defineStore('globalListStore', {
  state: () => ({
    settings: [],
    listUser: [],
    listCustomer: [],
    listDepartment: [],
    listJobPosition: [],
    listRole: [],
    listBank: [],
    listProvince: [],
    listPos: [],
    fundCategories: []
  }),

  getters: {
    // doubleCount: state => state.counter * 2
  },

  actions: {
    // 👉 fetch settings
    fetchSettings() {
        axios.get(`/settings`)
          .then(response => {
            this.settings = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list user
    fetchListUser() {
        axios.get(`/users/search`)
          .then(response => {
            this.listUser = response.data.users
            resolve(response)
          })
          .catch(error => reject(error))
    },

     // 👉 fetch list customer
     fetchListPos() {
        axios.get(`/list-pos`)
          .then(response => {
            this.listPos = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list customer
    fetchListCustomer() {
        axios.get(`/list-customers`)
          .then(response => {
            this.listCustomer = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list department
    fetchListDepartment() {
        axios.get(`/list-department`)
          .then(response => {
            this.listDepartment = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list job position
    fetchListJobPosition() {
        axios.get(`/list-jobPosition`)
          .then(response => {
            this.listJobPosition = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list role
    fetchListRole() {
        axios.get(`/list-roles`)
          .then(response => {
            this.listRole = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list bank
    fetchListBank() {
        axios.get(`/list-bank`)
          .then(response => {
            this.listBank = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list provinces
    fetchListProvince() {
        axios.get(`/provinces`)
          .then(response => {
            this.listProvince = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list district
    fetchListDistrict($provinceId) {
        axios.get(`/districts/${$provinceId}`)
          .then(response => {
            resolve(response)
          })
          .catch(error => reject(error))
    },

    // 👉 fetch list ward
    fetchListWard($districtId) {
        axios.get(`/wards/${$districtId}`)
          .then(response => {
            resolve(response)
          })
          .catch(error => reject(error))
    },

    fetchListFund() {
        axios.get(`/fund-categories`)
          .then(response => {
            this.fundCategories = response.data
            resolve(response)
          })
          .catch(error => reject(error))
    },
  }
})
