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
                                <span class="btn btn-secondary bd-toggle-animated-progress">{{ formatDate(new
                                    Date(items.from)) }} -- {{ formatDate(new Date(items.to)) }}</span>
                                <hr>
                                <div class="dropdown" bis_skin_checked="1">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                        data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-expanded="false">
                                        Bộ Lọc
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" bis_skin_checked="1">
                                        <label class="dropdown-item customize" v-on:click="hideForm = true">Tuỳ
                                            chỉnh</label>
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
                                <div class="row customize-row" v-show="hideForm" bis_skin_checked="1">
                                    <div class="col-md-4">
                                            <div class="form-group" >
                                                <flat-pickr v-model="from" :config="config"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" >
                                                <flat-pickr v-model="to" :config="config"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-sm btn-primary" @click="fetchAll()" type="submit">Lọc</button>
                                        </div>
                                </div>
                                <br>

                                <table id="example2" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col" class="sorting sorting_asc">Lô</th>
                                            <th scope="col">BILL</th>
                                            <th scope="col">Số tiền</th>
                                            <th scope="col">POS</th>
                                            <th scope="col">TÊN THẺ</th>
                                            <th scope="col">Phí</th>
                                            <th scope="col">Ship</th>
                                            <th scope="col">Tiền POS về</th>
                                            <th scope="col">Phí pos</th>
                                            <th scope="col">Tiền phí pos</th>
                                            <th scope="col">Tiền phí khách</th>
                                            <th scope="col">Lợi nhuận</th>
                                            <th>Tình trạng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-success">
                                            <td>{{ items.sl }}</td>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ formatPrice(items.total_money) }}</th>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ formatPrice(items.total_fee_ship) }}</th>
                                            <th scope="col">{{ formatPrice(items.total_pos_back) }}</th>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ formatPrice(items.total_fee_bank_money) }}</th>
                                            <th scope="col">{{ formatPrice(items.total_fee_customer) }}</th>
                                            <th scope="col">{{ formatPrice(items.total_profit) }}</th>
                                            <th>#</th>
                                        </tr>
                                        <tr v-for="(tran) in items.lists">
                                            <td>{{ tran.stt }}</td>
                                            <td>{{ tran.lo }}</td>
                                            <td>{{ tran.bill }}</td>
                                            <td>{{ formatPrice(tran.money) }}</td>
                                            <td>{{ (tran.pos_name) }}</td>
                                            <td>{{ (tran.customerCard) }}</td>
                                            <td>{{ (tran.fee_customer) }}</td>
                                            <td>{{ (tran.fee_ship ?? 0) }}</td>
                                            <td>{{ formatPrice(tran.money_back) }}</td>
                                            <td>{{ (tran.fee_bank) }}</td>
                                            <td>{{ formatPrice(tran.fee_bank_money) }}</td>
                                            <td>{{ formatPrice(tran.fee_customer_money) }}</td>
                                            <td>{{ formatPrice(tran.profit) }}</td>
                                            <td v-if="tran.bill_return"> <button class="btn btn-info">Đã về</button></td>
                                            <td v-if="!tran.bill_return">
                                                <i class=" operation-icon text-danger" @click="billReturn(tran)"
                                                    data-bs-toggle="modal" title="Xác nhận bill đã về"
                                                    data-bs-target="#modal-reDone"> Xác nhận bill về</i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-4 col-12" v-for="(pos) in items.posLists">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ pos.name }}</span>
                                                <span class="info-box-number">{{ formatPrice((parseInt(pos.profit)))
                                                }}</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 70%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Check -->
                                <div>
                                    <div class="modal fade" id="modal-reDone" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Xác nhận Bill đã về</h4>
                                                    <button type="button" class="close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group row text-left">
                                                        <p class="text-center"> Xác nhận thông tin<br>
                                                        </p>
                                                        <p>
                                                            <hr>
                                                            <span class="text-center"> <b>ID</b>: {{ dataBillReturn.id
                                                            }}</span>
                                                            <hr>
                                                            <b>Bill</b>: {{ dataBillReturn.bill }}
                                                            <hr>
                                                            <b>Lô</b>: {{ dataBillReturn.lo }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="modal-footer justify-content-between">
                                                    <button type="button" class="btn btn-default closeModalReDone"
                                                        data-bs-dismiss="modal">Huỷ</button>
                                                    <button  v-bind:disabled="loading"  @click="actionUpdate()" type="button" class="btn btn-primary">Đồng
                                                        ý</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
import { useTransactionListStore } from '@/pages/transaction/useTransactionListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import VueDatePicker from '@vuepic/vue-datepicker';
import moment from "moment";
import DataTable from 'datatables.net-vue3';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const from = ref()
const to = ref()

const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'd/m/Y',
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
const billReturn = dataInfo => {
    dataBillReturn.value.id = dataInfo.id
    dataBillReturn.value.type = dataInfo.type
    dataBillReturn.value.bill = dataInfo.bill
    dataBillReturn.value.lo = dataInfo.lo
}

const times = ref({ '0': '', '1': '', '2': '' })
const hideForm = ref(false)
const items = ref([])

const loading = ref(false)
const useStore = useTransactionListStore()
const dataBillReturn = ref({})
const setTimes = (time) => {
    times.value[0] = '';
    times.value[1] = '';
    times.value[3] = time;
    fetchAll()
};
const fetchAll = () => {
    loading.value = true;
    times.value['from'] = from.value
    times.value['to'] = to.value
    useStore.fetchTransactions(times.value).then(({ data }) => {
        loading.value = false;
        items.value = data.data
    }).catch(error => {
        // toast.error(error.message);
    })

};
fetchAll()
const timer = ref(1500)

const actionUpdate = () => {
    loading.value = false;
    useStore.updateData(dataBillReturn.value).then(response => {
        $('.closeModalReDone').click()
        fetchAll();
    }).catch(({ response }) => {
        loading.value = true;
        toast.error(response.data.message);
    })
}


</script>