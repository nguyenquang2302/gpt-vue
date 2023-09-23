<template>
    <div class="content-wrapper">
        <ContentHeader title="GIAO DỊCH"></ContentHeader>
        <div class="c-body">
            <div class="c-main">
                <div class="container-fluid">
                    <div class="fade-in">
                        <div class="card">
                            <div class="card-body">
                                Thống kê
                                <span class="btn btn-secondary bd-toggle-animated-progress" v-if="items">{{ formatDate(new Date(items.from)) }} -- {{  formatDate(new Date(items.to)) }}</span>
                                <hr>
                                <div class="dropdown" bis_skin_checked="1">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-bs-haspopup="true"
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
                                    </div>
                                </div>
                                <br>
                                <div class="row customize-row" v-show="hideForm"  bis_skin_checked="1">
                                        <div class="col-md-8">
                                            <div class="form-group" bis_skin_checked="1">
                                                <flat-pickr v-model="times" :config="config"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-sm btn-primary" @click="fetchAll()" type="submit">Lọc</button>
                                        </div>
                                </div>
                                <br>
                                
                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col" class="sorting sorting_asc">name</th>
                                            <th scope="col" class="sorting sorting_asc">creditAmount</th>
                                            <th scope="col" class="sorting sorting_asc">debitAmount</th>
                                            <th scope="col" class="sorting sorting_asc">Số tiền  tạm tính</th>
                                            <th scope="col" class="sorting sorting_asc">Từ KH</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody > 
                                   
                                        <tr v-for="(tran) in items.lists">
                                            <td>{{ tran.id }}</td>
                                            <td>{{ tran.name }}</td>
                                            <td>{{ formatPrice(tran.creditAmount) }}</td>
                                            <td>{{ formatPrice(tran.debitAmount) }}</td>
                                            <td>{{ formatPrice(tran.money_after) }}</td>
                                            <td>{{ (tran.note) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                               
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
    import { usePartnerListStore } from '@/pages/partner/usePartnerListStore'
    import { toast } from 'vue3-toastify';
    import { useGlobalStore } from '@/store/globalStore'
    import VueDatePicker from '@vuepic/vue-datepicker';
    import moment from "moment";
    import DataTable from 'datatables.net-vue3';
    import flatPickr from 'vue-flatpickr-component';
    import 'flatpickr/dist/flatpickr.css';

    const config = ref({
        wrap: true, // set wrap to true only when using 'input-group'
        altFormat: 'm/j/Y',
        altInput: true,
        dateFormat: 'Y-m-d',
    });

    const formatPrice = (value) => {
        const val = (value / 1).toFixed(0).replace(',', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    };
    const formatDate = (date) => {
        return moment(date).format("DD/MM/YYYY");
    }
    const times =  ref({'0':'','1':'','2':''})
    const userLogin = JSON.parse(localStorage.getItem('user'))
    const  hideForm = ref(false)
    const items = ref([])

    const loading = ref(false)
    const useStore = usePartnerListStore()
    const setTimes = (time) => {
        times.value[0] = '';
        times.value[1] = '';
        times.value[3] = time;
        fetchAll()
    };
    const fetchAll = () => {
        loading.value = true;
        useStore.fetchTransactions(times.value).then(({ data }) => {
            loading.value = false;
            items.value = data.data
        }).catch(error => {
            // toast.error(error.message);
        })

    };
    fetchAll()
    const timer = ref(1500)

</script>