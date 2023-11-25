import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import User from '../pages/users/User.vue'
import Customer from '../pages/customers/Customer.vue'
import TeleSalesCustomer from '../pages/TeleSalesCustomer/TeleSalesCustomer.vue'
import CustomerPantner from '../pages/customers/CustomerPantner.vue'
import CustomerCard from '../pages/customerCard/CustomerCard.vue'
import Drawals from '../pages/drawal/Drawal.vue'
import WithDrawals from '../pages/withDrawal/WithDrawal.vue'
import Transactions from '../pages/transaction/Transactions.vue'
import Expenses from '../pages/expense/Expense.vue'
import Globals from '../pages/global/Globals.vue'
import GlobalDetails from '../pages/global/GlobalDetails.vue'
import DashboardPos from '../pages/dashboardPos/DashboardPos.vue'
import Historys from '../pages/history/History.vue'
import Pos from '../pages/pos/Pos.vue'

import TransactionPartner from '../pages/partner/Partner.vue'


import Login from '../layouts/Login.vue'

const routes =  [
    {
        path: "/",
        component: Home,
        children :[
            {
                path:"/pos",
                name: "Pos",
                component: Pos
            },
            {
                path:"/user",
                name: "User",
                component: User
            }
            ,
            {
                path:"/customer",
                name: "Customer",
                component: Customer
            },
            {
                path:"/tele-sales-customers",
                name: "TeleSalesCustomer",
                component: TeleSalesCustomer
            },
            {
                path:"/customer-cards",
                name: "CustomerCard",
                component: CustomerCard
            },
            {
                path:"/drawals",
                name: "Drawals",
                component: Drawals
            }
            ,
            {
                path:"/withdrawals",
                name: "WithDrawals",
                component: WithDrawals
            },
            {
                path:"/transactions",
                name: "Transaction",
                component: Transactions
            },
            {
                path:"/expenses",
                name: "Expense",
                component: Expenses
            },
            {
                path:"/history",
                name: "History",
                component: Historys
            },
            {
                path:"/dashboard-pos",
                name: "DashboardPos",
                component: DashboardPos
            },
            {
                path:"/globals",
                name: "Global",
                component: Globals
            },
            {
                path:"/global-details",
                name: "GlobalDetail",
                component: GlobalDetails
            },
            {
                path:"/customer-partners",
                name: "CustomerPantner",
                component: CustomerPantner
            },
            {
                path:"/transaction-partners",
                name: "TransactionPartner",
                component: TransactionPartner
            }
        ]
    },
    {
        path:"/login",
        name: "Login",
        component: Login
    },
    
]

let router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;