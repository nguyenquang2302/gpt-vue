import axios from '@axios'
import { defineStore } from 'pinia';
import { useAppAbility } from '@/plugins/casl/useAppAbility'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    userData: {},
    accessToken: null,
    userPermissions: {}
  }),

  getters: {
    // userData: state => state.user
  },

  actions: {
    login(data) {
      data.device_name = 'web_admin'
      return new Promise((resolve, reject) => {
        axios.post('/login', data).then((response) => {
          let { user, token, permissions } = response.data
          this.userData = user
          this.accessToken = token
          this.userPermissions = permissions
          // const ability = useAppAbility()
          // ability.update(JSON.stringify(permissions))
          localStorage.setItem('userData', JSON.stringify(user))

          localStorage.setItem('userPermissions', JSON.stringify(permissions))
          localStorage.setItem('accessToken', token)
          resolve(response)
        }).catch(error => reject(error))
      })
    },

    changePassword(data) {
      return new Promise((resolve, reject) => {
        axios.post('/changePassword', data).then((response) => {resolve(response)}).catch(error => reject(error))
      })
    },

    passwordRemind(email) {
      return axios.post('/password/remind', { email })
    },

    authInfo() {
      return new Promise((resolve, reject) => {
        axios.get(`/me`)
          .then((resp) => {
            if (resp && resp.data) {
              let { user, permissions } = resp.data
              this.userData = user
              this.userPermissions = permissions
              // const ability = useAppAbility()
              // ability.update(JSON.stringify(permissions))
              localStorage.setItem('userData', JSON.stringify(user))
              localStorage.setItem('userPermissions', JSON.stringify(permissions))
              resolve(resp.data)
            } else {
              return router.replace('/login')
            }
          })
          .catch(error => reject(error))
      })
    },

    logout() {
      return new Promise((resolve, reject) => {
        axios.post('/logout').then((response) => {
          let {success} = response.data
          if (success) {
            this.userData = {}
            this.accessToken = null
            this.userPermissions = {}
            // const ability = useAppAbility()
            // ability.update({})
            localStorage.removeItem('userData')
            localStorage.removeItem('userPermissions')
            localStorage.removeItem('accessToken')
            resolve('Success')
          }
        }).catch(error => reject(error))
      })
    }
  }
})

