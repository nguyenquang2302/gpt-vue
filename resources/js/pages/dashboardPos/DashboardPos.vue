<template>
    <div class="content-wrapper">
        <ContentHeader title="Thống kê POS"></ContentHeader>
        <div class="content">
            <div class="c-main">
                <div class="container-fluid">
                    <div class="fade-in">
                        <div class="card">
                            <div class="card-body">
                                Thống kê POS
                                <span class="btn btn-secondary bd-toggle-animated-progress" v-if="items.from" >{{ formatDate(new Date(items.from)) }} -- {{  formatDate(new Date(items.to)) }}</span>
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
                                            <li class="dropdown-item" v-on:click="setTimes('sub7Day')">Hôm qua</li>

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
                                <div v-if="items.status && !loading">
                                    <hr>
                                    <h3 class="mx-2 my-5 text-center"> ALL</h3>
                                    <div>
                                        <div class="row">
                                            <div class="col-lg-4 col-12">
                                                <div class="info-box bg-warning" >
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content" >
                                                        <span class="info-box-text">Đã về</span>
                                                        <span class="info-box-number">{{ number_format(items.total) }}</span>
                                                        <div class="progress" >
                                                            <div class="progress-bar" style="width: 70%" ></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="info-box bg-warning" >
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content" >
                                                        <span class="info-box-text">Đã về</span>
                                                        <span class="info-box-number">{{ number_format(items.pos_back_money) }}</span>
                                                        <div class="progress" >
                                                            <div class="progress-bar" style="width: 70%" ></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="info-box bg-danger" >
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content" >
                                                        <span class="info-box-text">Chưa về</span>
                                                        <span class="info-box-number">{{ number_format(items.total - items.pos_back_money) }}</span>
                                                        <div class="progress" >
                                                            <div class="progress-bar" style="width: 70%" ></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div  v-if="items.status && !loading" v-for="(detail, loop) in items.all_pos">
                                    <hr>
                                    <h3 class="text-center mx-2 my-5">{{ detail.name }}</h3>
                                    <div class="row">
                                        <div class="col-lg-4 col-12">
                                            <div class="info-box bg-warning" >
                                                <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                <div class="info-box-content" >
                                                    <span class="info-box-text">Tổng</span>
                                                    <span class="info-box-number">{{ number_format(detail.total) }}</span>
                                                    <div class="progress" >
                                                        <div class="progress-bar" style="width: 70%" ></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-12">
                                            <div class="info-box bg-warning" >
                                                <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                <div class="info-box-content" >
                                                    <span class="info-box-text">Đã về</span>
                                                    <span class="info-box-number">{{ number_format(detail.pos_back_money) }}</span>
                                                    <div class="progress" >
                                                        <div class="progress-bar" style="width: 70%" ></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-12">
                                            <div class="info-box bg-danger" >
                                                <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                <div class="info-box-content" >
                                                    <span class="info-box-text">Chưa về</span>
                                                    <span class="info-box-number">{{ number_format(detail.total - detail.pos_back_money) }}</span>
                                                    <div class="progress" >
                                                        <div class="progress-bar" style="width: 70%" ></div>
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
        <div  class="loading-fix" v-if="loading"> 
            <DotLoader />
        </div>
    </div>
</template>


<script lang="ts" setup>

import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
import type { Header, Item, ServerOptions, SortType } from "vue3-easy-data-table";
import ContentHeader from '@/layouts/ContentHeader.vue'
import { useDashboardPosListStore } from '@/pages/dashboardPos/useDashboardPosListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import moment from "moment";
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'm/j/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

import vue3Spinner from 'vue3-spinner'
import { DotLoader } from "vue3-spinner";

const items = ref([])
const number_format = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};
const formatDate = (date) => {
    return moment(date).format("DD/MM/YYYY");
}
const times = ref({ '0': '', '1': '', '2': '' })
const from = ref()
const to = ref()
const userLogin = JSON.parse(localStorage.getItem('user'))
const  hideForm = ref(false)


const loading = ref(false)
const useStore = useDashboardPosListStore()
const globalStore = useGlobalStore()

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
    useStore.fetchDashboardPos(times.value).then(({ data }) => {
        items.value = data.data
        loading.value = false
    }).catch(error => {
        toast.error(error.message);
    })

};
</script>