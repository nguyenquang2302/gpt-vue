<template>
    <div class="content-wrapper">
        <ContentHeader title="THU CHI"></ContentHeader>
        <div class="c-body">
            <div class="c-main">
                <div class="container-fluid">
                    <div class="fade-in">
                        <div class="card">
                            <div class="card-body">
                                Thống kê
                                <span class="btn btn-secondary bd-toggle-animated-progress" v-if="items.from">{{ formatDate(new Date(items.from)) }} -- {{  formatDate(new Date(items.to)) }}</span>
                                <hr>
                                <div class="dropdown" bis_skin_checked="1">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Bộ Lọc
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                        bis_skin_checked="1">
                                        <label class="dropdown-item customize" v-on:click="hideForm = true">Tuỳ chỉnh</label>
                                        <li class="dropdown-item" v-on:click="setTimes('toDay')">Hôm nay</li>
                                        <li class="dropdown-item" v-on:click="setTimes('tomorrow')">Hôm qua</li>

                                        <li class="dropdown-item" v-on:click="setTimes('sub7Day')">7 Ngày
                                            qua</li>

                                        <li class="dropdown-item" v-on:click="setTimes('sub14Day')">14 ngày
                                            qua</li>
                                        <li class="dropdown-item" v-on:click="setTimes('sub30Day')">30 ngày
                                            qua</li>

                                        <li class="dropdown-item" v-on:click="setTimes('thisMonth')">Tháng này</li>
                                        <li class="dropdown-item" v-on:click="setTimes('lastMonth')">Tháng
                                            Trước</li>

                                        <li class="dropdown-item" v-on:click="setTimes('thisWeek')">Tuần này</li>
                                        <li class="dropdown-item" v-on:click="setTimes('lastWeek')">Tuần
                                            trước</li>
                                            <li class="dropdown-item" v-on:click="setTimes('all')">Tất cả</li>
                                    </div>
                                </div>
                                <br>
                                <div class="row customize-row" v-show="hideForm"  bis_skin_checked="1">
                                        <div class="col-md-8">
                                            <div class="form-group" bis_skin_checked="1">
                                                
                                                <VueDatePicker :timezone="'Asia/Ho_Chi_Minh'"   :enable-time-picker="true" :range="true"
                                                
                                                :clearable="true" :month-change-on-scroll="false" 
                                                    auto-apply  v-model="times"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-sm btn-primary" @click="fetchAll()" type="submit">Lọc</button>
                                        </div>
                                </div>
                                <br>
                                
                                <table  class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col" class="sorting sorting_asc">Tên</th>
                                            <th scope="col">Loại</th>
                                            <th scope="col">Danh mục</th>
                                            <th scope="col">Thu</th>
                                            <th scope="col">Chi</th>
                                            <th scope="col">thời gian</th>
                                            <th scope="col">Nội dung</th>
                                        </tr>
                                    </thead>
                                    <tbody > 
                                        <tr class="bg-success">
                                            <td>#</td>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>
                                            <th scope="col">{{formatPrice(items.totalCreditAmount)}}</th>
                                            <th scope="col">{{formatPrice(items.totalDebitAmount)}}</th>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>

                                        </tr>

                                        <tr v-for="(tran) in items.expences">
                                            <td>{{ tran.id }}</td>
                                            <td>{{ tran.name }}</td>
                                            <td>{{ tran.type== 1?'Thu':'Chi'  }}</td>
                                            <td>{{ tran.fund_category?tran.fund_category.name:'' }}</td>
                                            <td>{{ formatPrice(tran.creditAmount) }}</td>
                                            <td>{{ formatPrice(tran.debitAmount) }}</td>
                                            <td>{{ formatDate(tran.created_at) }}</td>
                                            <td>{{ (tran.bank_log.note) }}</td>
                                        </tr>
                                       
                                        
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>


<script lang="ts" setup>

    import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
    import type { Header, Item, ServerOptions, SortType } from "vue3-easy-data-table";
    import ContentHeader from '@/layouts/ContentHeader.vue'
    import { useExpenseListStore } from '@/pages/expense/useExpenseListStore'
    import { toast } from 'vue3-toastify';
    import { useGlobalStore } from '@/store/globalStore'
    import VueDatePicker from '@vuepic/vue-datepicker';
    import moment from "moment";
    import { useRoute } from 'vue-router';

    const route = useRoute();
    
    const formatPrice = (value) => {
        const val = (value / 1).toFixed(0).replace(',', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    };
    const formatDate = (date) => {
        return moment(date).format("DD/MM/YYYY hh:mm");
    }
    const times =  ref({'0':'','1':'','2':'','3':''})
    const userLogin = JSON.parse(localStorage.getItem('user'))
    const  hideForm = ref(false)
    const items = ref([])

    const loading = ref(false)
    const serverItemsLength = ref(0);
    const reRender = ref(0);
    const useStore = useExpenseListStore()
    const globalStore = useGlobalStore()
    const searchValue = ref( {
            isChecked:'',
            search:''
        })
    const setTimes = (time) => {
        times.value[0] = '';
        times.value[1] = '';
        times.value[3] = time;
        fetchAll()
    };
    const fetchAll = () => {
        loading.value = true;
        if(route.query.type =='operate') {
            searchValue.value.isChecked = 'operate'
        }
        if(route.query.type =='invest') {
            searchValue.value.isChecked = 'invest'
        }
        items.value = []
        useStore.fetchExpenses(times.value,searchValue.value).then(({ data }) => {
            loading.value = false;
            items.value = data.data
        }).catch(error => {
            // toast.error(error.message);
        })

    };
    fetchAll()
    const timer = ref(500)


// watch(searchValue, async (newQuestion, oldQuestion) => {
//     if (timer.value) {
//         clearTimeout(timer.value);
//         timer.value = 0;
//     }
//     timer.value = setTimeout(() => {
//         fetchAll()
//     }, 500);
// }, { deep: true })
// branchssudo systemctl enable nginx

</script>