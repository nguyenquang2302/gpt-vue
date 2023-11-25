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
                        <!-- <div class="text-right col-md-6">
                            <button class="card-header-action" data-bs-toggle="modal" data-bs-target="#modal-add-new"><i
                                    class="fa fa-plus"></i> Thêm </button>
                        </div> -->
                    </div>
                </div>
                <div class="card-body">

                    <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">

                       

                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-view"></i>
                                <!-- <i class="fa fa-edit operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                <i class="fa fa-trash operation-icon text-danger" @click="askDelete(item)" v-if="userLogin.type=='admin'"
                                    data-bs-toggle="modal" title="Xoá KH" data-bs-target="#modal-delete"></i>
                                    <i class="fa fa-plus operation-icon text-info" title="Thêm thẻ KH" @click="askAddCard(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-add-customer-card"></i> -->
                                 
                                    
                            </div>
                        </template>
                    </EasyDataTable>

                    <!-- add Customer -->

                    <!-- <div>
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
                                                <flat-pickr  :class="'from-control'" v-model="userDataAddNew.birth_day"/>
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
                    </div> -->
                    
                    <!-- end -->
                    <!-- DeleteUser -->

                    <!-- <div>
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
                    </div> -->
                    <!-- end -->
                    <!-- Edit User -->

                    <!-- <div>
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
                                                <v-select  v-model="userData.province_id"
                                                    :options="globalStore.listProvince" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Quận/Huyện (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select  v-model="userData.district_id"
                                                    :options="listDistricts" label="name"
                                                    :reduce="district => district.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phường/Xã (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select  v-model="userData.ward_id"
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
                                                <flat-pickr :class="'from-control'" v-model="userCard.birth_day" :config="config"/>
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
                    </div> -->
                    <!-- end -->
                    <!-- view customer Card -->
                     <div>
                        <div class="modal right fade"  id="modal-view"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Xem Thông tin POS</h4>
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
                                            <img :src="userData.urlQR" alt="" srcset="">
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
                   
                </div>
            </div>
        </div>
    </div>
</template>


<script lang="ts" setup>

import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
import type { Header, Item, ServerOptions, SortType } from "vue3-easy-data-table";
import ContentHeader from '@/layouts/ContentHeader.vue'
import { usePosListStore } from '@/pages/pos/usePosListStore'
import { useCustomerCardListStore } from '@/pages/customerCard/useCustomerCardListStore'
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
const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};

const userLogin  = JSON.parse(localStorage.getItem('user'))
const type = route.query.type;
const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    // { text: "Chi nhánh", value: "branch.name" },
    { text: "Tên", value: "name" },
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
    rowsPerPage: 100,
    sortBy: 'id',
    sortType: 'desc',
});
const objDefault = {

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



const useStore = usePosListStore()
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
    useStore.fetchPosId(idEdit.value).then(({ data }) => {
        userData.value = data.pos
    }).catch(error => {
        // toast.error(error.message);
    })
    // userData.value = dataInfo
}



const fetchAll = () => {
    loading.value = true;
   
    if(route.query.type =='invest') {
        searchValue.value.isChecked = 'invest'
    }
    useStore.fetchPos(serverOptions.value, searchValue.value).then(({ data }) => {
        serverItemsLength.value = data.poss.total
        loading.value = false;
        items.value = data.poss.data

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
    // userDataAddNew.value.name= '',
    // userDataAddNew.value.province_id = '',
    // userDataAddNew.value.district_id = '',
    // userDataAddNew.value.ward_id = '',
    // userDataAddNew.value.phone = '',
    // userDataAddNew.value.address = '',
    // userDataAddNew.value.cmnd = '',
    // userDataAddNew.value.active =  true
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
