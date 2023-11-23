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
                                <div v-if="item.user"  :class="[(item.user.type === 'partner')?'text-red':'']">{{ (item.user.name) }}</div>
                            </div>
                        </template>

                        <template #item-schedule="item">
                            <div class="operation-wrapper" v-if="item.schedule">
                                <div v-if="item.schedule[0]">{{ (item.schedule[0].note) }}</div>

                            </div>
                        </template>
                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-view"></i>
                                <i class="fa fa-edit operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                    <i class="fas fa-clock operation-icon text-info" title="Hẹn qua chi nhánh" @click="askAddSchedule(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-add-Schedule"></i>
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
                                                <label for="name" class="col-form-label">Tình Trạng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select v-model="userDataAddNew.status_type" class="form-control">
                                                    <option value="1">Khách có nhu cầu</option>
                                                    <option value="2">Khách chưa có nhu cầu</option>
                                                    <option value="3">Khách không nghe máy</option>
                                                    <option value="4">Khách không có thẻ</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nguồn khách hàng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select v-model="userDataAddNew.source_type" class="form-control">
                                                    <option value="1">Facebook</option>
                                                    <option value="2">Website</option>
                                                    <option value="3">Tự kiếm</option>
                                                    <option value="4">Giới thiệu</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>
                                        

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">CMND/CCCD</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.cmnd" name="cmnd"
                                                    class="form-control" placeholder="CMND/CCCD" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ghi chú</label>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea type="text" v-model="userDataAddNew.note" 
                                                    class="form-control"></textarea>
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
                                                <label for="name" class="col-form-label">Tình Trạng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select v-model="userData.status_type" class="form-control">
                                                    <option value="1">Khách có nhu cầu</option>
                                                    <option value="2">Khách chưa có nhu cầu</option>
                                                    <option value="3">Khách không nghe máy</option>
                                                    <option value="4">Khách không có thẻ</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nguồn khách hàng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select v-model="userData.source_type" class="form-control">
                                                    <option value="1">Facebook</option>
                                                    <option value="2">Website</option>
                                                    <option value="3">Tự kiếm</option>
                                                    <option value="4">Giới thiệu</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ghi chú</label>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea type="text" v-model="userData.note" 
                                                    class="form-control"></textarea>
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
                                                <label for="name" class="col-form-label">Tình Trạng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select readonly v-model="userData.status_type" class="form-control">
                                                    <option value="1">Khách có nhu cầu</option>
                                                    <option value="2">Khách chưa có nhu cầu</option>
                                                    <option value="3">Khách không nghe máy</option>
                                                    <option value="4">Khách không có thẻ</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nguồn khách hàng</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select readonly v-model="userData.source_type" class="form-control">
                                                    <option value="1">Facebook</option>
                                                    <option value="2">Website</option>
                                                    <option value="3">Tự kiếm</option>
                                                    <option value="4">Giới thiệu</option>
                                                    <option value="10">Khác</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ghi chú</label>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea type="text" readonly v-model="userData.note" 
                                                    class="form-control"></textarea>
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
                        <div class="modal right fade" id="modal-add-Schedule" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">THÊM MỚI LỊCH HẸN</h4>
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
                                                <input type="text" v-model="schedule.name" name="name"
                                                    class="form-control" placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày hẹn <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <flat-pickr  class="from-control" v-model="schedule.schedule"  :config="configtime"/>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Note (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea class="form-control" v-model="schedule.note" ></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Chi nhánh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="schedule.branch_id" :options="allBranchs" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalCreate"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionScheduleCreate()">Đồng
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
import { useTeleSalesCustomerListStore } from '@/pages/TeleSalesCustomer/useTeleSalesCustomerListStore'
import { useCustomerScheduleStore } from '@/pages/TeleSalesCustomer/useCustomerScheduleStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import { useRoute } from 'vue-router';
import jquery from 'jquery';

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const route = useRoute();

const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'd/m/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

const configtime = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'd/m/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};
import { useBranchListStore } from '@/pages/branchs/useBranchListStore'

const useBranchs = useBranchListStore()
const userLogin  = JSON.parse(localStorage.getItem('user'))
const type = route.query.type;
const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "Chi nhánh", value: "branch.name" },
    { text: "Tên", value: "name" },
    { text: "Số dư", value: "money", sortable: true },
    { text: "Người tạo", value: "user", sortable: true },
    { text: "Hẹn", value: "schedule", sortable: true },
    { text: "Operation", value: "operation" },
];

const formatDate = (date) => {
    const tzOffset = date.getTimezoneOffset() * 60 * 1000
    return new Date(date - tzOffset).toISOString().split('T')[0]
}

const items = ref([])



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

const schedule = ref({
})

const useStore = useTeleSalesCustomerListStore()
const customerScheduleStore = useCustomerScheduleStore()
const globalStore = useGlobalStore()
const searchValue = ref( {
    isChecked:'',
    search:''
})

const showDetail = dataInfo => {
    userData.value = dataInfo
}
const provinceId = ref('')
const allBranchs = ref([])

const askDelete = dataInfo => {
    idDelete.value = dataInfo.id
}

const fetchAllBranch = () => {
    loading.value = true;
    useBranchs.fetchAllBranchs().then(({ data }) => {
        loading.value = false;
        allBranchs.value = data.branchs
    }).catch(error => {
        toast.error(error.message);
    })
};
fetchAllBranch()

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

const askAddSchedule = dataInfo => {
    userData.value = dataInfo;
    schedule.value.customer_id = dataInfo.id
}

const fetchAll = () => {
    loading.value = true;
    if(route.query.type =='debit') {
        searchValue.value.isChecked = false
    }
    if(route.query.type =='credit') {
        searchValue.value.isChecked = true
    }
    if(route.query.type =='invest') {
        searchValue.value.isChecked = 'invest'
    }
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
const clearCreate = () => {
    userDataAddNew.value.name= '',
    userDataAddNew.value.province_id = '',
    userDataAddNew.value.district_id = '',
    userDataAddNew.value.ward_id = '',
    userDataAddNew.value.phone = '',
    userDataAddNew.value.address = '',
    userDataAddNew.value.cmnd = '',
    userDataAddNew.value.active =  true
}
const actionCreate = () => {
    loading.value = false;
    useStore.addCustomer(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalCreate').click()
        fetchAll()
        clearCreate();

    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}

const actionScheduleCreate = () => {
    loading.value = false;
    customerScheduleStore.addSchedule(schedule.value).then(response => {
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
const timer = ref(500)

watch(serverOptions, (value,value2) => {
    if(value != value2) {
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            fetchAll()
        }, 500);
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
    }, 500);
}, { deep: true })
// branchssudo systemctl enable nginx

</script>
