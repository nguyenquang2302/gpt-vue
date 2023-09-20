<template>
    <div class="content-wrapper">
        <ContentHeader title="Tài khoản"></ContentHeader>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions row">
                        <div class="card-header-actions col-md-6">
                            <div class="input-group mb-3" bis_skin_checked="1">
                                <input type="text" class="form-control" v-model="searchValue.search"
                                    placeholder="Nhập từ khoá ...">
                                <div class="input-group-append">
                                    <button class="input-group-text" @click="fetchAll()"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-right col-md-6">
                            <button class="card-header-action" data-bs-toggle="modal" data-bs-target="#modal-add-new"><i
                                    class="fa fa-plus"></i> Thêm </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="justify-content-between">
                        <button type="button"  class="btn  mx-2 my-2" :class="[(searchValue.isChecked === '') ? 'btn-primary' : 'btn-default']" data-bs-dismiss="modal" @click="setSearchValue('')"> Tất cả </button>
                        <button type="button"  class="btn  mx-2 my-2" :class="[(searchValue.isChecked == true) ? 'btn-primary' : 'btn-default']" data-bs-dismiss="modal" @click="setSearchValue(true)"> Dư </button>
                        <button type="button"  class="btn " :class="[(searchValue.isChecked === false) ? 'btn-primary' : 'btn-default']" @click="setSearchValue(false)" > Nợ</button>
                    </div>

                    <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">

                        <template #item-money="item">
                            <div class="operation-wrapper">
                                <div>{{ formatPrice(item.money) }}</div>

                            </div>
                        </template>
                        <template #item-user="item">
                            <div class="operation-wrapper">
                                <div v-if="item.user"  :class="[(item.user.type === 'partner')?'text-red':'']">{{ (item.name) }}</div>
                            </div>
                        </template>

                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-view"></i>
                                <i class="fa fa-edit operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                <i class="fa fa-trash operation-icon text-danger" @click="askDelete(item)" v-if="userLogin.type=='admin'"
                                    data-bs-toggle="modal" title="Xoá KH" data-bs-target="#modal-delete"></i>
                                    <i class="fa fa-plus operation-icon text-info" title="Thêm thẻ KH" @click="askAddCard(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-add-customer-card"></i>
                                    <router-link
                                        :to="{
                                            name: 'CustomerCard',
                                            query: {
                                                customer_id: item.id
                                            }
                                        }">
                                        <i class="fa fa-credit-card operation-icon text-info" title="Thẻ Khách hàng" ></i>
                                    </router-link>

                                    <router-link
                                        :to="{
                                            name: 'Drawals',
                                            query: {
                                                customer_id: item.id
                                            }
                                        }">
                                        <i class="fa fa-download operation-icon text-info" ></i>
                                    </router-link>
                                    
                                    <router-link
                                        :to="{
                                            name: 'WithDrawals',
                                            query: {
                                                customer_id: item.id
                                            }
                                        }">
                                        <i class="fa  fa-upload operation-icon text-info" title="Đáo hạn"></i>
                                    </router-link>
                                    
                            </div>
                        </template>
                    </EasyDataTable>

                    <!-- add Customer -->

                    <div>
                        <div class="modal right fade" id="modal-add-new" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Thêm mới tài khoản</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.name" name="name"
                                                    class="form-control" placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">SĐT (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.phone" name="phone"
                                                    class="form-control" placeholder="Nhập SĐT" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">CMND/CCCD (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.cmnd" name="cmnd"
                                                    class="form-control" placeholder="CMND/CCCD" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tỉnh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select   v-model="userDataAddNew.province_id"
                                                    :options="globalStore.listProvince" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Quận/Huyện (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select   v-model="userDataAddNew.district_id"
                                                    :options="listDistricts" label="name"
                                                    :reduce="district => district.id">
                                                </v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phường/Xã (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.ward_id"
                                                    :options="listWards" label="name"
                                                    :reduce="ward => ward.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Địa chỉ (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.address" name="address"
                                                    class="form-control" placeholder="Nhập địa chỉ" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">BirthDay <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueDatePicker  v-model="userDataAddNew.birth_day" :enable-time-picker="false"
                                                    :clearable="false" :month-change-on-scroll="false" :format="formatDate"
                                                    :timezone="'Asia/Novosibirsk'" auto-apply />

                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalCreate"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionCreate()">Đồng
                                            ý</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- end -->
                    <!-- DeleteUser -->

                    <div>
                        <div class="modal fade" id="modal-delete" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Xoá tài khoản</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row text-center">
                                            <p> Bạn chắc chắn muốn xoá tài khoản <br> ID : {{ idDelete }} </p>

                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closeModalDelete"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionDelete()">Đồng
                                            ý</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                    <!-- Edit User -->

                    <div>
                        <div class="modal right fade"  id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Chỉnh sửa KH</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.name" name="name"
                                                    class="form-control" placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">SĐT (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.phone" name="phone"
                                                    class="form-control" placeholder="Nhập SĐT" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">CMND/CCCD (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.cmnd" name="cmnd"
                                                    class="form-control" placeholder="CMND/CCCD" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tỉnh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select readonly disabled v-model="userData.province_id"
                                                    :options="globalStore.listProvince" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Quận/Huyện (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select readonly disabled v-model="userData.district_id"
                                                    :options="listDistricts" label="name"
                                                    :reduce="district => district.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phường/Xã (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select readonly disabled v-model="userData.ward_id"
                                                    :options="listWards" label="name"
                                                    :reduce="ward => ward.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Địa chỉ (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.address" name="address"
                                                    class="form-control" placeholder="Nhập địa chỉ" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueDatePicker v-model="userCard.birth_day" :enable-time-picker="false"
                                                    :clearable="false" :month-change-on-scroll="false" :format="formatDate"
                                                    :timezone="'Asia/Novosibirsk'" auto-apply />

                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalEdit"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionUpdate()">Đồng
                                            ý</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                    <!-- view customer Card -->
                     <div>
                        <div class="modal right fade"  id="modal-view"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Xem Thông tin KH</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.name" name="name"
                                                    class="form-control" placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">SĐT (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.phone" name="phone"
                                                    class="form-control" placeholder="Nhập SĐT" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">CMND/CCCD (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.cmnd" name="cmnd"
                                                    class="form-control" placeholder="CMND/CCCD" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tỉnh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.province_id"
                                                    :options="globalStore.listProvince" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Quận/Huyện (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.district_id"
                                                    :options="listDistricts" label="name"
                                                    :reduce="district => district.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phường/Xã (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.ward_id"
                                                    :options="listWards" label="name"
                                                    :reduce="ward => ward.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Địa chỉ (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.address" name="address"
                                                    class="form-control" placeholder="Nhập địa chỉ" maxlength="100" required />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalEdit"
                                            data-bs-dismiss="modal">Huỷ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div>
                        <div class="modal right fade" id="modal-add-customer-card" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Thêm Thẻ KHÁCH HÀNG</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div>{{ userData.name }} - {{ userData.phone }}</div>
                                        <hr>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userCard.name" name="name"
                                                    class="form-control" placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">4 số đầu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userCard.start_number" name="start_number"
                                                    class="form-control" placeholder="Nhập 4 số đầu" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">4 số cuối (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userCard.end_number" name="end_number"
                                                    class="form-control" placeholder="Nhập 4 số cuối" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày sao kê</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userCard.day_statement" name="day_statement"
                                                    class="form-control" placeholder="Ngày sao kê" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày Tới hạn</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userCard.due_date" name="due_date"
                                                    class="form-control" placeholder="Ngày tới hạn" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số thẻ</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userCard.card_number" name="card_number"
                                                    class="form-control" placeholder="Số thẻ" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Hạn mức</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userCard.currency_limit"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false  }"
                                                ></VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân hàng (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select  v-model="userCard.bank_id" :options="globalStore.listBank"
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalCreate"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionCardCreate()">Đồng
                                            ý</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                </div>
            </div>
        </div>
    </div>
</template>


<script lang="ts" setup>

import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
import type { Header, Item, ServerOptions, SortType } from "vue3-easy-data-table";
import ContentHeader from '@/layouts/ContentHeader.vue'
import { useCustomerListStore } from '@/pages/customers/useCustomerListStore'
import { useCustomerCardListStore } from '@/pages/customerCard/useCustomerCardListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import jquery from 'jquery';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};

const userLogin  = JSON.parse(localStorage.getItem('user'))

const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "Chi nhánh", value: "branch.name" },
    { text: "Tên", value: "name" },
    { text: "Số dư", value: "money", sortable: true },
    { text: "Người tạo", value: "user", sortable: true },
    { text: "Operation", value: "operation" },
];

const formatDate = (date) => {
    const tzOffset = date.getTimezoneOffset() * 60 * 1000
    return new Date(date - tzOffset).toISOString().split('T')[0]
}

const items = ref([])
const allBranchs = ref([])
const serverOptions = ref<ServerOptions>({
    page: 1,
    rowsPerPage: 5,
    sortBy: 'id',
    sortType: 'desc',
});
const objDefault = {
    name: '',
    province_id:'',
    district_id:'',
    ward_id:'',
    phone:'',
    address:'',
    cmnd:'',
    active: true
}


const loading = ref(false)
const serverItemsLength = ref(0);
const reRender = ref(0);
const isPopupDelete = ref(false);
const idDelete = ref(0);

const isPopupEdit = ref(false);
const idEdit = ref(0);
const userData = ref({ ...objDefault })
const userDataAddNew = ref({ ...objDefault })

const userCard = ref({
    'name': '',
    'start_number': '',
    'end_number': '',
    'day_statement': 0,
    'due_date': 0,
    'card_number': '',
    'currency_limit': '',
    'customer_id': '',
    'bank_id': '',
    'note': '',
    'birth_day': '',
    active: true,
})

const useStore = useCustomerListStore()
const useCardStore = useCustomerCardListStore()
const globalStore = useGlobalStore()
const searchValue = ref( {
    isChecked:'',
    search:''
})
const showDetail = dataInfo => {
    userData.value = dataInfo
}
const provinceId = ref('')

const askDelete = dataInfo => {
    idDelete.value = dataInfo.id
}

const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    useStore.fetchCustomer(idEdit.value).then(({ data }) => {
        userData.value = data.customer
    }).catch(error => {
        // toast.error(error.message);
    })
    // userData.value = dataInfo
}

const askAddCard = dataInfo => {
    userData.value = dataInfo;
    userCard.value.customer_id = dataInfo.id
}

const fetchAll = () => {
    loading.value = true;
    useStore.fetchCustomers(serverOptions.value, searchValue.value).then(({ data }) => {
        serverItemsLength.value = data.customers.total
        loading.value = false;
        items.value = data.customers.data

    }).catch(error => {
        // toast.error(error.message);
    })

};

const setSearchValue = (value) => {
    searchValue.value.isChecked = value;
} 

const actionDelete = () => {
    loading.value = false;
    useStore.deleteData(idDelete.value).then(response => {
        idDelete.value = 0
        toast.success('Xóa thành công');
        loading.value = true;
        jquery('.closeModalDelete').click()
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}
const actionUpdate = () => {
    loading.value = false;
    useStore.updateData(userData.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalEdit').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const actionCreate = () => {
    loading.value = false;
    useStore.addCustomer(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}

const actionCardCreate = () => {
    loading.value = false;
    useCardStore.addCustomerCard(userCard.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
fetchAll()
globalStore.fetchListProvince()
globalStore.fetchListBank()
// watch(
//     () => serverOptions, (value) => {
//         console.log(value)
//         fetchAll()
//     }, { deep: true }
// );
// watch([serverOptions,userDataAddNew], (value,value2) => {
//     console.log(value,value2)
// }, { deep: true }
// );
const timer = ref(1500)

watch(serverOptions, (value,value2) => {
    if(value != value2) {
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            fetchAll()
        }, 1500);
    }
}, { deep: true }
);
const listDistricts =ref([]);
const listWards =ref([]);
const districtId =ref('');
watch(userDataAddNew, async (newQuestion, oldQuestion) => {
    if(newQuestion.province_id !=provinceId.value) {
        globalStore.fetchListDistrict(newQuestion.province_id).then(response => {
            listDistricts.value =response.data
        })
        provinceId.value = newQuestion.province_id
    }
    if(newQuestion.district_id != districtId.value) {
        globalStore.fetchListWard(newQuestion.district_id).then(response => {
            listWards.value =response.data
        })
        districtId.value = newQuestion.district_id
    }

}, { deep: true })

watch(userData, async (newQuestion, oldQuestion) => {
    if(newQuestion.province_id !=provinceId.value) {
        globalStore.fetchListDistrict(newQuestion.province_id).then(response => {
            listDistricts.value =response.data
        })
        provinceId.value = newQuestion.province_id
    }
    if(newQuestion.district_id != districtId.value) {
        globalStore.fetchListWard(newQuestion.district_id).then(response => {
            listWards.value =response.data
        })
        districtId.value = newQuestion.district_id
    }

}, { deep: true })
watch(searchValue, async (newQuestion, oldQuestion) => {
    if (timer.value) {
        clearTimeout(timer.value);
        timer.value = 0;
    }
    timer.value = setTimeout(() => {
        fetchAll()
    }, 1500);
}, { deep: true })
// branchssudo systemctl enable nginx

</script>
