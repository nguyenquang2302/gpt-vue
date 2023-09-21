<template>
    <div class="content-wrapper">
        <ContentHeader title="Tài khoản"></ContentHeader>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions text-right">
                        <button class="card-header-action"  data-bs-toggle="modal" data-bs-target="#modal-add-new" ><i class="fa fa-plus"></i> Thêm </button>
                    </div>
                </div>
                <div class="content">

                    <EasyDataTable  v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">
                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-edit operation-icon" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                
                                <i class="fa fa-bell operation-icon" @click="askChangePass(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-change-pass"></i>
                                <i class="fa fa-trash operation-icon text-danger" @click="askDelete(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-delete"></i>
                                
                            </div>
                        </template>
                    </EasyDataTable>

                     <!-- add User -->

                     <div>
                        <div class="modal right fade"  id="modal-add-new"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Cập nhật tài khoản</h4>
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
                                                <input type="text" v-model="userDataAddNew.name" name="name" class="form-control"
                                                    placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div><!--form-group-->
                                        <div class="form-group row" bis_skin_checked="1">
                                            <label for="name" class="col-md-4 col-form-label">Kiểu</label>
                                            <div class="col-md-8" bis_skin_checked="1">
                                                <select v-model="userDataAddNew.type" name="type" class="form-control" required=""
                                                    x-on:change="userType = $event.target.value">
                                                    <option value="user" selected="">Người dùng</option>
                                                    <option value="admin">ADMIN</option>
                                                    <option value="manager">Manager</option>
                                                    <option value="manager_vip">Manager VIP</option>
                                                    <option value="manager_vip_2">Manager VIP 2</option>
                                                    <option value="mod">Mod</option>
                                                    <option value="staff">Staff</option>
                                                    <option value="pos">POS</option>
                                                    <option value="partner">Đối tác</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div v-if="userDataAddNew.type =='partner'">
                                            <div class="form-group row" >
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Thời gian đối tác</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <flat-pickr v-model="userDataAddNew.time_partner" :config="config"/>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Phí mặc định</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <VueNumberFormat  :class="'form-control'"
                                                        v-model:value="userDataAddNew.fee_partner"
                                                        :options="{ precision: 2, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                    </VueNumberFormat>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group row" v-if="userDataAddNew.type =='pos'">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên POS</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.posName" :options="allBranchs" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Mật khẩu</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="password" v-model="userDataAddNew.password" 
                                                    class="form-control" placeholder="Nhập mật khẩu" maxlength="255"
                                                    required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Chi nhánh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.branch_id" :options="allBranchs" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Chi nhánh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select multiple v-model="userDataAddNew.branch_ids" :options="allBranchs"
                                                    label="name" :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Email (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.email" name="email"
                                                    class="form-control" placeholder="Nhập Email" maxlength="255"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Auto PAY POS (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="check-box-customize">
                                                    <input
                                                        class="custom-control-input custom-control-input-danger custom-control-input-outline"
                                                        type="checkbox" v-model="userDataAddNew.autoPosBack"
                                                        id="customCheckbox-User-Edit">
                                                    <label for="customCheckbox-User-Edit" class="custom-control-label">Tự
                                                        động</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân Hàng MB (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="check-box-customize">
                                                    <input
                                                        class="custom-control-input custom-control-input-danger custom-control-input-outline"
                                                        type="checkbox" v-model="userDataAddNew.activeBank"
                                                        id="customCheckbox-User-Edit-MB">
                                                    <label for="customCheckbox-User-Edit-MB"
                                                        class="custom-control-label">Kích
                                                        hoạt</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-show="userDataAddNew.activeBank">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Tên Đăng nhập</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" v-model="userDataAddNew.accountName" name="accountName"
                                                        class="form-control" placeholder="Tên đăng nhập MB" maxlength="255"
                                                        required />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="name" class="col-form-label">Tài Khoản MB</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" v-model="userDataAddNew.benAccountNo"
                                                            name="accountName" class="form-control"
                                                            placeholder="Tài khoản MB" maxlength="255" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="name" class="col-form-label">Mật khẩu MB</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="password" v-model="userDataAddNew.passBank" name="passBank"
                                                            class="form-control" placeholder="Mật khẩu MB" maxlength="255"
                                                            required />
                                                    </div>
                                                </div>
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
                        <div class="modal right fade" id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Cập nhật tài khoản</h4>
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
                                                <input type="text" v-model="userData.name" name="name" class="form-control"
                                                    placeholder="Nhập Tên" maxlength="100" required />
                                            </div>
                                        </div><!--form-group-->
                                        <div class="form-group row" bis_skin_checked="1">
                                            <label for="name" class="col-md-4 col-form-label">Kiểu</label>
                                            <div class="col-md-8" bis_skin_checked="1">
                                                <select v-model="userData.type" name="type" class="form-control" required=""
                                                    x-on:change="userType = $event.target.value">
                                                    <option value="user" selected="">Người dùng</option>
                                                    <option value="admin">Người quản lý</option>
                                                    <option value="manager">Manager</option>
                                                    <option value="manager_vip">Manager VIP</option>
                                                    <option value="manager_vip_2">Manager VIP 2</option>
                                                    <option value="mod">Mod</option>
                                                    <option value="staff">Staff</option>
                                                    <option value="pos">POS</option>
                                                    <option value="partner">Đối tác</option>

                                                </select>
                                            </div>
                                        </div>

                                        <div v-if="userData.type =='partner'">
                                            <div class="form-group row" >
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Thời gian đối tác</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <flat-pickr v-model="userData.time_partner" :config="config"/>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Phí mặc định</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <VueNumberFormat  :class="'form-control'"
                                                        v-model:value="userData.fee_partner"
                                                        :options="{ precision: 2, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                    </VueNumberFormat>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group row" v-if="userData.type=='pos'">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên POS</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.posName" :options="allBranchs" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Chi nhánh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.branch_id" :options="allBranchs" label="name"
                                                    :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Chi nhánh (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select multiple v-model="userData.branch_ids" :options="allBranchs"
                                                    label="name" :reduce="branch => branch.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Email (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.email" name="email"
                                                    class="form-control" placeholder="Nhập Email" maxlength="255"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Auto PAY POS (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="check-box-customize">
                                                    <input
                                                        class="custom-control-input custom-control-input-danger custom-control-input-outline"
                                                        type="checkbox" v-model="userData.autoPosBack"
                                                        id="customCheckbox-User-Edit">
                                                    <label for="customCheckbox-User-Edit" class="custom-control-label">Tự
                                                        động</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân Hàng MB (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="check-box-customize">
                                                    <input
                                                        class="custom-control-input custom-control-input-danger custom-control-input-outline"
                                                        type="checkbox" v-model="userData.activeBank"
                                                        id="customCheckbox-User-Edit-MB">
                                                    <label for="customCheckbox-User-Edit-MB"
                                                        class="custom-control-label">Kích
                                                        hoạt</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-show="userData.activeBank">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Tên Đăng nhập</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" v-model="userData.accountName" name="accountName"
                                                        class="form-control" placeholder="Tên đăng nhập MB" maxlength="255"
                                                        required />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="name" class="col-form-label">Tài Khoản MB</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" v-model="userData.benAccountNo"
                                                            name="accountName" class="form-control"
                                                            placeholder="Tài khoản MB" maxlength="255" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="name" class="col-form-label">Mật khẩu MB</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="password" v-model="userData.passBank" name="passBank"
                                                            class="form-control" placeholder="Mật khẩu MB" maxlength="255"
                                                            required />
                                                    </div>
                                                </div>
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
                    <!-- change - pass -->
                    <div>
                        <div class="modal right fade" id="modal-change-pass" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Thay đổi mật khâủ</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Mật khẩu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="password" v-model="userData.password" name="name"
                                                    class="form-control" placeholder="Mật khẩu mới" maxlength="100"
                                                    required />
                                            </div>
                                        </div><!--form-group-->
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nhập lại mật khẩu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="password" v-model="userData.password_confirm" name="name"
                                                    class="form-control" placeholder="Nhập lại khẩu mới" maxlength="100"
                                                    required />
                                            </div>
                                        </div><!--form-group-->
                                    </div>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default CloseModalChangePass"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionChangePass()">Đồng
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
</template>


<script lang="ts" setup>

import { watch, defineComponent, toRefs, reactive, ref, onMounted } from "vue"
import type { Header, Item, ServerOptions } from "vue3-easy-data-table";
import ContentHeader from '@/layouts/ContentHeader.vue'
import { useUserListStore } from '@/pages/users/useUserListStore'
import { useBranchListStore } from '@/pages/branchs/useBranchListStore'
import { toast } from 'vue3-toastify';
import jquery from 'jquery';

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';


const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'm/j/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

const headers: Header[] = [
    { text: "ID", value: "id" },
    { text: "Tên", value: "name" },
    { text: "Email", value: "email" },
    { text: "Operation", value: "operation" },
];


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
    body: '',
    active: true
}

// test


const loading = ref(false)
const serverItemsLength = ref(0);
const reRender = ref(0);
const isPopupDelete = ref(false);
const idDelete = ref(0);

const isPopupEdit = ref(false);
const idEdit = ref(0);
const userData = ref({ ...objDefault })
const userDataAddNew = ref({ ...objDefault })

const useStore = useUserListStore()
const useBranchs = useBranchListStore()
const showDetail = dataInfo => {
    userData.value = dataInfo
}

const formatDate = (date) => {
    const tzOffset = date.getTimezoneOffset() * 60 * 1000
    return new Date(date - tzOffset).toISOString().split('T')[0]
}

const askDelete = dataInfo => {
    idDelete.value = dataInfo.id
}
const askChangePass = dataInfo => {
    userData.value = dataInfo
}
const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    useStore.fetchUser(idEdit.value).then(({ data }) => {
        userData.value = data.user
    }).catch(error => {
        // toast.error(error.message);
    })
    // userData.value = dataInfo
}

const fetchAll = () => {
    loading.value = true;
    useStore.fetchUsers(serverOptions.value).then(({ data }) => {
        serverItemsLength.value = data.users.total
        loading.value = false;
        items.value = data.users.data

    }).catch(error => {
        // toast.error(error.message);
    })

};


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
    useStore.addUser(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const actionChangePass = () => {
    loading.value = false;
    useStore.changePass(userData.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalChangePass').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
fetchAll()

watch(serverOptions, (value) => {
    fetchAll()
}, { deep: true }
);

// branchssudo systemctl enable nginx


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

</script>