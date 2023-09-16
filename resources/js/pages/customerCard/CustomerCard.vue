<template>
    <div class="content-wrapper">
        <ContentHeader title="Tài khoản"></ContentHeader>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions row">
                        <div class="card-header-actions col-md-6">
                            <div class="input-group mb-3" bis_skin_checked="1">
                                <input type="text" class="form-control" v-model="searchValue"
                                    placeholder="Nhập từ khoá ...">
                                <div class="input-group-append">
                                    <button class="input-group-text" @click="fetchAll()"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-right col-md-6" v-show="userDataAddNew.customer_id">
                            <button class="card-header-action" data-bs-toggle="modal" data-bs-target="#modal-add-new" @click="askCreate()"><i
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

                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-show"></i>
                                <i class="fa fa-edit operation-icon" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                <i class="fa fa-trash operation-icon text-danger" @click="askDelete(item)" v-if="userLogin.type=='admin'"
                                    data-bs-toggle="modal" title="Xoá Thẻ KH" data-bs-target="#modal-delete"></i>
                            </div>
                        </template>
                    </EasyDataTable>
                    

                     <!-- add customer Card -->
                     <div>
                        <div class="modal right fade" id="modal-add-new" aria-hidden="true">
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
                                        <div>{{ userDataAddNew.name }}</div>
                                        <hr>
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
                                                <label for="name" class="col-form-label">4 số đầu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.start_number" name="start_number"
                                                    class="form-control" placeholder="Nhập 4 số đầu" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">4 số cuối (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.end_number" name="end_number"
                                                    class="form-control" placeholder="Nhập 4 số cuối" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày sao kê</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userDataAddNew.day_statement" name="day_statement"
                                                    class="form-control" placeholder="Ngày sao kê" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày Tới hạn</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userDataAddNew.due_date" name="due_date"
                                                    class="form-control" placeholder="Ngày tới hạn" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số thẻ</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userDataAddNew.card_number" name="card_number"
                                                    class="form-control" placeholder="Số thẻ" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Hạn mức</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.currency_limit"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false  }"
                                                ></VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân hàng (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select  v-model="userDataAddNew.bank_id" :options="globalStore.listBank"
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
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
                                        <h4 class="modal-title">Xoá Thẻ KH </h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row text-center">
                                            <p> Bạn chắc chắn muốn xoá  Thẻ <br> ID : {{ idDelete }} </p>
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
                    <!-- Edit Card -->

                    <div>
                        <div class="modal right fade" id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Sửa Thẻ KHÁCH HÀNG</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div>{{ userData.name }}</div>
                                        <hr>
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
                                                <label for="name" class="col-form-label">4 số đầu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.start_number" name="start_number"
                                                    class="form-control" placeholder="Nhập 4 số đầu" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">4 số cuối (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.end_number" name="end_number"
                                                    class="form-control" placeholder="Nhập 4 số cuối" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày sao kê</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userData.day_statement" name="day_statement"
                                                    class="form-control" placeholder="Ngày sao kê" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày Tới hạn</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userData.due_date" name="due_date"
                                                    class="form-control" placeholder="Ngày tới hạn" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số thẻ</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userData.card_number" name="card_number"
                                                    class="form-control" placeholder="Số thẻ" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Hạn mức</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userData.currency_limit"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false  }"
                                                ></VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân hàng (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select  v-model="userData.bank_id" :options="globalStore.listBank"
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
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
                    <div>
                        <div class="modal right fade" id="modal-show"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Xem Thẻ KHÁCH HÀNG</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div>{{ userData.name }}</div>
                                        <hr>
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
                                                <label for="name" class="col-form-label">4 số đầu (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.start_number" name="start_number"
                                                    class="form-control" placeholder="Nhập 4 số đầu" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">4 số cuối (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userData.end_number" name="end_number"
                                                    class="form-control" placeholder="Nhập 4 số cuối" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày sao kê</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="number" v-model="userData.day_statement" name="day_statement"
                                                    class="form-control" placeholder="Ngày sao kê" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày Tới hạn</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="number" v-model="userData.due_date" name="due_date"
                                                    class="form-control" placeholder="Ngày tới hạn" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số thẻ</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="number" v-model="userData.card_number" name="card_number"
                                                    class="form-control" placeholder="Số thẻ" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Hạn mức</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly :class="'form-control'"
                                                    v-model:value="userData.currency_limit"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false  }"
                                                ></VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân hàng (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select readonly disabled  v-model="userData.bank_id" :options="globalStore.listBank"
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
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
import { useCustomerCardListStore } from '@/pages/customerCard/useCustomerCardListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import { useRoute } from 'vue-router';
import jquery from 'jquery';

const route = useRoute();
const userLogin  = JSON.parse(localStorage.getItem('user'))
const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};

const sortBy: string[] = ["name", "money"];
const sortType: SortType[] = ["desc", "asc"];

const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "Tên thẻ", value: "name" },
    { text: "4 số đầu", value: "start_number" },
    { text: "4 số cuối", value: "end_number" },
    { text: "Khách hàng", value: "customer.name", sortable: true },
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
    'name': '',
    'start_number': '',
    'end_number': '',
    'day_statement': 0,
    'due_date': 0,
    'card_number': '',
    'currency_limit': '',
    'customer_id': ref(),
    'bank_id': '',
    'note': '',
    active: true,
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
userDataAddNew.value.customer_id = route.query.customer_id ?? '';
const useCustomerCardStore = useCustomerCardListStore()
const globalStore = useGlobalStore()
const searchValue = ref()
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
    useCustomerCardStore.fetchCustomerCard(idEdit.value).then(({ data }) => {
        userData.value = data.customer_card
    }).catch(error => {
        // toast.error(error.message);
    })
    // userData.value = dataInfo
}

const fetchAll = () => {
    loading.value = true;
    useCustomerCardStore.fetchCustomerCards(serverOptions.value, searchValue.value,route.query.customer_id).then(({ data }) => {
        serverItemsLength.value = data.customer_cards.total
        loading.value = false;
        items.value = data.customer_cards.data

    }).catch(error => {
        // toast.error(error.message);
    })

};


const actionDelete = () => {
    loading.value = false;
    useCustomerCardStore.deleteData(idDelete.value).then(response => {
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
    useCustomerCardStore.updateData(userData.value).then(response => {
        loading.value = true
        jquery('.CloseModalEdit').click()
        console.log(response.data)
        toast.success(response.data.msg)
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const actionCreate = () => {
    loading.value = false;
    useCustomerCardStore.addCustomerCard(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.msg)
        jquery('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}


fetchAll()
globalStore.fetchListBank()

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