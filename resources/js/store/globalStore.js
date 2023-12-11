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
    fundCategories: [],
    posLists:[]
  }),

  getters: {
    // doubleCount: state => state.counter * 2
  },

  actions: {
    // 👉 fetch settings
    fetchSettings() {
      return new Promise((resolve, reject) => {
        axios.get(`/settings`)
          .then(response => {
            this.settings = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list user
    fetchListUser() {
      return new Promise((resolve, reject) => {
        axios.get(`/users/search`)
          .then(response => {
            this.listUser = response.data.users
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

     // 👉 fetch list customer
    //  fetchListPos() {
    //   return new Promise((resolve, reject) => {
    //     axios.get(`/list-pos`)
    //       .then(response => {
    //         this.listPos = response.data
    //         resolve(response)
    //       })
    //       .catch(error => reject(error))
    //   })
    // },

    // 👉 fetch list customer
    fetchListCustomer() {
      return new Promise((resolve, reject) => {
        axios.get(`/list-customers`)
          .then(response => {
            this.listCustomer = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list department
    fetchListDepartment() {
      return new Promise((resolve, reject) => {
        axios.get(`/list-department`)
          .then(response => {
            this.listDepartment = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list job position
    fetchListJobPosition() {
      return new Promise((resolve, reject) => {
        axios.get(`/list-jobPosition`)
          .then(response => {
            this.listJobPosition = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list role
    fetchListRole() {
      return new Promise((resolve, reject) => {
        axios.get(`/list-roles`)
          .then(response => {
            this.listRole = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list bank
    fetchListBank() {
      return new Promise((resolve, reject) => {
        axios.get(`/list-bank`)
          .then(response => {
            this.listBank = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list provinces
    fetchListProvince() {
      return new Promise((resolve, reject) => {
        axios.get(`/provinces`)
          .then(response => {
            this.listProvince = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list district
    fetchListDistrict($provinceId) {
      return new Promise((resolve, reject) => {
        axios.get(`/districts/${$provinceId}`)
          .then(response => {
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    // 👉 fetch list ward
    fetchListWard($districtId) {
      return new Promise((resolve, reject) => {
        axios.get(`/wards/${$districtId}`)
          .then(response => {
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    fetchListFund() {
      return new Promise((resolve, reject) => {
        axios.get(`/fund-categories`)
          .then(response => {
            this.fundCategories = response.data
            resolve(response)
          })
          .catch(error => reject(error))
      })
    },

    fetchListPos() {
      return new Promise((resolve, reject) => {
        axios.get(`/pos_lists`)
          .then(response => {
            this.posLists = response.data
            this.listPos = response.data

            resolve(response)
          })
          .catch(error => reject(error))
      })
    }
  }
})
