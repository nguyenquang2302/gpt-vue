<template>
    <div class="content-wrapper">
        <ContentHeader title="Thống kê chung"></ContentHeader>
        <div class="content">
            <div class="c-main">
                <div class="container-fluid">
                    <div class="fade-in">
                        <div class="card">
                            <div class="card-body">
                                Thống kê
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">

                                <div class="row " v-if="items.settings">

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng tiền đầu tư</span>
                                                <span class="info-box-number">{{ number_format(items.settings.investors) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng vốn dự kiến</span>
                                                <span class="info-box-number">{{ number_format(parseInt(items.settings.investors) + parseInt(items.settings.total_ship) + parseInt(items.settings.total_profit)  - parseInt(items.settings.total_expense) - parseInt(items.settings.total_expense2) ) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng vốn thực</span>
                                                <span class="info-box-number">{{ number_format(parseInt(items.settings.money_auth)-parseInt(items.settings.total_money_minus)-parseInt(items.settings.total_money_plus)) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-warning" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng Nợ phải trả</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_money_plus) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-danger" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng nợ phải đòi</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_money_minus) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-danger" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng tiền chưa về</span>
                                                <span class="info-box-number">{{ number_format(items.settings.money_not_back_yet) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-warning" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng ( chưa về + đang giữ)</span>
                                                <span class="info-box-number">{{ number_format(items.settings.money_auth) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-danger" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">TIỀN (chưa về + đang giữ + ký quỹ + công nợ)</span>
                                                <span class="info-box-number">{{ number_format(items.settings.money_auth_2) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-danger" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng Tiền chuyển vào chưa xử lý</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_banklogs_credit) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-warning" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng Tiền Chuyển đi Chưa xử lý</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_banklogs_debit) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng chi phí vận hành</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_expense) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-orange" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Tổng chi phí đầu tư</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_expense2) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-orange" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Lợi nhuận</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_profit) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-orange" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Ship</span>
                                                <span class="info-box-number">{{ number_format(items.settings.total_ship) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="col-lg-4 col-6">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">Số thẻ đang giữ</span>
                                                <span class="info-box-number">{{ number_format(items.settings.customer_cart_save) }}</span>
                                                <div class="progress" >
                                                    <div class="progress-bar" style="width: 70%" ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <hr>
                                <div class="card-body">
                                    <h3>Tài Khoản</h3>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-6" v-for="(detail, loop) in items.users">
                                        <div class="info-box bg-success" >
                                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                            <div class="info-box-content" >
                                                <span class="info-box-text">{{ detail.name }}</span>
                                                <span class="info-box-number">{{ number_format(detail.money_last) }}</span>
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
</template>


<script lang="ts" setup>

import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
import type { Header, Item, ServerOptions, SortType } from "vue3-easy-data-table";
import ContentHeader from '@/layouts/ContentHeader.vue'
import { useGlobalListStore } from '@/pages/global/useGlobalListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import moment from "moment";

const items = ref([])
const number_format = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};
const formatDate = (date) => {
    return moment(date).format("DD/MM/YYYY");
}
const times = ref({ '0': '', '1': '', '2': '' })
const userLogin = JSON.parse(localStorage.getItem('user'))


const loading = ref(false)
const useStore = useGlobalListStore()
const globalStore = useGlobalStore()

const fetchAll = () => {
    loading.value = true;
    useStore.fetchGlobals().then(({ data }) => {
        items.value = data.data

    }).catch(error => {
        // toast.error(error.message);
    })

};
fetchAll()

</script>