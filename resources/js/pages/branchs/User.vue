<template>
    <div class="content-wrapper">
        <ContentHeader title="Tài khoản"></ContentHeader>
        <div class="content">
            <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                table-class-name="customize-table">
                <template #item-operation="item">
                    <div class="operation-wrapper">
                        <i class="fa fa-edit operation-icon" @click="askEdit(item)" data-toggle="modal" data-target="#modal-edit"></i>
                        <i class="fa fa-trash operation-icon" @click="askDelete(item)" data-toggle="modal" data-target="#modal-delete"></i>
                    </div>
                </template>
            </EasyDataTable>

            <!-- DeleteUser -->

            <div>
                <div class="modal fade" id="modal-delete" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Xoá tài khoản</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row text-center">
                                    <p> Bạn chắc chắn muốn xoá tài khoản <br> ID : {{ idDelete }} </p>

                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" @click="isPopupDelete = false"
                                    data-dismiss="modal">Huỷ</button>
                                <button type="button" class="btn btn-primary" @click="actionDelete()">Đồng ý</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User -->

            <div>
                <div class="modal right fade" id="modal-edit" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Cập nhật tài khoản</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isPopupEdit = false">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="name" class="col-form-label">Tên (*)</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text"  v-model="userData.name" name="name" class="form-control" placeholder="Nhập Tên"
                                             maxlength="100" required  />
                                    </div>
                                </div><!--form-group-->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" @click="isPopupEdit = false"
                                    data-dismiss="modal">Huỷ</button>
                                <button type="button" class="btn btn-primary" @click="actionUpdate()">Đồng ý</button>
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
import { toast } from 'vue3-toastify';

const headers: Header[] = [
    { text: "ID", value: "id" },
    { text: "Tên", value: "name" },
    { text: "Email", value: "email" },
    { text: "Operation", value: "operation" },
];

const items = ref([])
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


const loading = ref(false)
const serverItemsLength = ref(0);
const reRender = ref(0);
const isPopupDelete = ref(false);
const idDelete = ref(0);

const isPopupEdit = ref(false);
const idEdit = ref(0);
const userData = ref({...objDefault})

const useStore = useUserListStore()
const showDetail = dataInfo => {
  userData.value = dataInfo
}


const askDelete = dataInfo => {
    isPopupDelete.value = true
    idDelete.value = dataInfo.id
}
const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    userData.value = dataInfo
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
        isPopupDelete.value = false
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}

fetchAll()
watch(serverOptions, (value) => {
    fetchAll()
}, { deep: true }
);

</script>