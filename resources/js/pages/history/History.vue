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
                    </div>
                </div>
                <div class="card-body">
                        <div class="justify-content-between">
                            <button type="button"  class="btn  mx-2 my-2" :class="[(searchValue.isChecked === '') ? 'btn-primary' : 'btn-default']" data-bs-dismiss="modal" @click="setSearchValue('')"> Tất cả </button>
                            <button type="button"  class="btn  mx-2 my-2" :class="[(searchValue.isChecked == true) ? 'btn-primary' : 'btn-default']" data-bs-dismiss="modal" @click="setSearchValue(true)"> Xác nhận </button>
                            <button type="button"  class="btn " :class="[(searchValue.isChecked === false) ? 'btn-primary' : 'btn-default']" @click="setSearchValue(false)" > Chưa xác nhận</button>
                        </div>
                    <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">

                        <template #item-creditAmount="item">
                            <div class="operation-wrapper">
                                <div>{{ formatPrice(item.creditAmount) }}</div>

                            </div>
                        </template>

                        <template #item-debitAmount="item">
                            <div class="operation-wrapper">
                                <div>{{ formatPrice(item.debitAmount) }}</div>

                            </div>
                        </template>
                        <template #item-created_at="item">
                            <div class="operation-wrapper">
                                <div>{{ formatDate(item.created_at) }}</div>

                            </div>
                        </template>
                        <template #item-isChecked="item">
                            <div class="operation-wrapper">
                                <div>{{ item.isChecked?'Đã xác nhận':'Chưa'}}</div>

                            </div>
                        </template>
                        
                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Xem" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-view"></i>
                                <i class="fa fa-edit operation-icon" v-show="!item.isChecked" title="Chỉnh sửa" @click="askEdit(item)" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit"></i>
                                    
                            </div>
                        </template>
                    </EasyDataTable>

                    <!-- end -->
                    <!-- Edit User -->

                    <div>
                        <div class="modal right fade"  id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Chỉnh sửa GD</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">refNo (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly disabled v-model="userData.refNo" name="refNo"
                                                    class="form-control" placeholder="refNo" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Accountno (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly disabled v-model="userData.accountNo" name="Accountno"
                                                    class="form-control" placeholder="Accountno" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">transactionDate (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly disabled v-model="userData.transactionDate" name="transactionDate"
                                                    class="form-control" placeholder="transactionDate" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">debitAmount (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userData.debitAmount"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">creditAmount (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userData.creditAmount"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">content (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly disabled v-model="userData.content" name="content"
                                                    class="form-control" placeholder="content" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">description (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly disabled v-model="userData.description" name="description"
                                                    class="form-control" placeholder="description" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row" v-if="userLogin.type=='admin'" >
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">benAccountNo (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text"  v-model="userData.benAccountNo" name="benAccountNo"
                                                    class="form-control" placeholder="benAccountNo" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nội dung chỉnh sửa (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.content_fix" name="content_fix"
                                                    class="form-control" placeholder="content_fix" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div v-if="userData.content_fix =='THUCHI'">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Tiêu đề (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" v-model="userData.name" name="name"
                                                        class="form-control" placeholder="name" maxlength="100" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Nội dung chi (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" v-model="userData.note" name="note"
                                                        class="form-control" placeholder="note" maxlength="100" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Danh mục (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <v-select v-model="userData.fund_category_id"
                                                        :options="globalStore.fundCategories" label="name"
                                                        :reduce="fundCategory => fundCategory.id"></v-select>
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
                    <!-- SHOW User -->

                    <div>
                        <div class="modal right fade"  id="modal-view"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">XEM giao dịch</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">refNo (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.refNo" name="refNo"
                                                    class="form-control" placeholder="refNo" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Accountno (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.accountNo" name="Accountno"
                                                    class="form-control" placeholder="Accountno" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">transactionDate (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.transactionDate" name="transactionDate"
                                                    class="form-control" placeholder="transactionDate" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">debitAmount (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userData.debitAmount"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">creditAmount (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userData.creditAmount"
                                                    :options="{ precision: 0, prefix: '', suffix: ' ', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">content (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.content" name="content"
                                                    class="form-control" placeholder="content" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">description (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.description" name="description"
                                                    class="form-control" placeholder="description" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row" v-if="userLogin.type=='admin'" >
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">benAccountNo (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text"  v-model="userData.benAccountNo" name="benAccountNo"
                                                    class="form-control" placeholder="benAccountNo" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nội dung chỉnh sửa (*)</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disable type="text" v-model="userData.content_fix" name="content_fix"
                                                    class="form-control" placeholder="content_fix" maxlength="100" required />
                                            </div>
                                        </div>
                                        <div v-if="userData.content_fix =='THUCHI'">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Tiêu đề (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input readonly disable type="text" v-model="userData.name" name="name"
                                                        class="form-control" placeholder="name" maxlength="100" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Nội dung chi (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input readonly disable type="text" v-model="userData.note" name="note"
                                                        class="form-control" placeholder="note" maxlength="100" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="name" class="col-form-label">Danh mục (*)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <v-select readonly disable v-model="userData.fund_category_id"
                                                        :options="globalStore.fundCategories" label="name"
                                                        :reduce="fundCategory => fundCategory.id"></v-select>
                                                </div>
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
import { useHistoryListStore } from '@/pages/history/useHistoryListStore'
import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import moment from "moment";
import jquery from 'jquery';


const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};

const formatDate = (date) => {
        return moment(date).format("DD/MM/YYYY hh:mm");
}

const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "accountNo", value: "accountNo" },
    { text: "refNo", value: "refNo" },
    { text: "created_at", value: "created_at" },
    { text: "transactionDate", value: "transactionDate" },
    
    { text: "creditAmount", value: "creditAmount", sortable: true },
    { text: "debitAmount", value: "debitAmount", sortable: true },
    { text: "content", value: "content" },
    { text: "description", value: "description" },
    { text: "user", value: "user.name" },
    { text: "SỬA", value: "content_fix" },
    { text: "isChecked", value: "isChecked" },

    { text: "Operation", value: "operation" },
];

const items = ref([])
const serverOptions = ref<ServerOptions>({
    page: 1,
    rowsPerPage: 25,
    sortBy: 'id',
    sortType: 'desc',
});
const objDefault = {
    'refNo': ''
   
}


const loading = ref(false)
const serverItemsLength = ref(0);
const reRender = ref(0);
const isPopupDelete = ref(false);
const idDelete = ref(0);

const isPopupEdit = ref(false);
const idEdit = ref(0);
const userData = ref({ ...objDefault })

const useStore = useHistoryListStore()
const globalStore = useGlobalStore()
const searchValue = ref(
{
    search:'',
    isChecked:''

})
const showDetail = dataInfo => {
    userData.value = dataInfo
}
const provinceId = ref('')
const userLogin  = JSON.parse(localStorage.getItem('user'))

const setSearchValue = (value) => {
    searchValue.value.isChecked = value;
} 

const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    useStore.fetchHistory(idEdit.value).then(({ data }) => {
        userData.value = data.banklog
    }).catch(error => {
        // toast.error(error.message);
    })
    // userData.value = dataInfo
}

const fetchAll = () => {
    loading.value = true;
    useStore.fetchHistorys(serverOptions.value, searchValue.value).then(({ data }) => {
        serverItemsLength.value = data.banklogs.total
        loading.value = false;
        items.value = data.banklogs.data

    }).catch(error => {
        // toast.error(error.message);
    })

};

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

fetchAll()
globalStore.fetchListFund()
const timer = ref(1500)

watch(serverOptions, (value,value2) => {
    if(value != value2) {
    items.value = []
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
watch(searchValue, async (newQuestion, oldQuestion) => {
    items.value = []
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