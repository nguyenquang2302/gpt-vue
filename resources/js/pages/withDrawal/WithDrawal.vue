<template>
    <div class="content-wrapper">
        <ContentHeader title="Tài khoản"></ContentHeader>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions row">
                        <div class="card-header-actions col-md-6">
                            <div class="input-group mb-3" >
                                <input type="text" class="form-control" v-model="searchValue"
                                    placeholder="Nhập từ khoá ...">
                                <div class="input-group-append">
                                    <button class="input-group-text" @click="fetchAll()"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-right col-md-6">
                            <button class="card-header-action" data-bs-toggle="modal" data-bs-target="#modal-add-new">
                                <i class="fa fa-plus"></i> Thêm </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">

                        <template #item-id="item">
                            <div class="operation-wrapper">
                                <div>
                                    <i title="Chỉnh sửa"  @click="askShow(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-show">{{ (item.id) }}</i>
                                </div>                                    
                            </div>
                        </template>
                        
                        <template #item-money_withdrawal="item">
                            <div class="operation-wrapper">
                                <div>{{ formatPrice(item.money_withdrawal) }}</div>

                            </div>
                        </template>

                        <template #item-user="item">
                            <div class="operation-wrapper">
                                <div>{{ dateTime(item.user.name) }}</div>

                            </div>
                        </template>

                        <template #item-created_at="item">
                            <div v-if="item.user" class="operation-wrapper">
                                <div>{{ (item.created_at) }}</div>

                            </div>
                        </template>

                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa"  @click="askShow(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-show"></i>
                                <i class="fa fa-edit operation-icon" title="Chỉnh sửa" v-if="!item.isDone" @click="askEdit(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-edit"></i>
                                <i class="fa fa-trash operation-icon text-danger" v-if="!item.isDone" @click="askDelete(item)"
                                    data-bs-toggle="modal" title="Xoá Thẻ KH" data-bs-target="#modal-delete"></i>
                                <i class="fas fa-sync operation-icon text-danger" v-if="item.isDone" @click="asReDone(item)"
                                    data-bs-toggle="modal" title="Cho phép sửa/xoá" data-bs-target="#modal-reDone"></i>
                            </div>
                        </template>
                    </EasyDataTable>


                    <!-- add customer Card -->
                    <div>
                        <div class="modal right fade" id="modal-add-new" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">KHÁCH HÀNG</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label" @click="fetchCustomerFirst" >Khách hàng <span class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.customer_id" :options="customerSearch"  :filterable="false"
                                                    label="name_phone" :reduce="customer => customer.id"
                                                    @search="fetchCustomer" @search:focus="fetchCustomer" @option:selected="selectedCustomer" ></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">Thẻ<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="userDataAddNew.customer_card_id" :options="listCards" 
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>
                                        <hr>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">STT <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userDataAddNew.stt" name="name"
                                                    class="form-control" placeholder="stt" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.name" name="name"
                                                    class="form-control" placeholder="Nhập tên gd" maxlength="100"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">NV SHIP <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.user_fee_id"  
                                                    :options="globalStore.listUser" label="name"
                                                    :reduce="customer => customer.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tiền đáo <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.money_withdrawal"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí ship <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.fee_ship"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí nhân viên <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.fee_user"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [%] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.fee_customer"
                                                    :options="{ precision: 2, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [vnđ] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.fee_money_customer"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <flat-pickr v-model="userDataAddNew.datetime" :config="config"/>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-header">
                                            <div class="form-group row" >
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết đáo hạn</label>
                                            </div>
                                        </div>
                                        <br>
                                        <div v-for="(detail, loop) in userDataAddNew.details">
                                            
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="text-center">Lần: <span class="loop ">{{ loop + 1 }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-right"> 
                                                    <button class="fa fa-trash operation-icon text-danger" title="Xoá Thẻ KH" @click="removeItem(userDataAddNew,loop)"></button>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">POS<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="detail.pos_id" :options="globalStore.listPos" 
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Nạp<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.money"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Rút<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.money_drawal"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Lô.Bill<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.group_bill"
                                                                    :options="{ precision: 3, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                        </div>
                                        <div class="col-md-8" >
                                            <label class="btn btn-sm btn-primary float-left add-detail" type="button "
                                                @click="addItemAddNew()">Thêm</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền sẽ rút :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(userDataAddNew.money_withdrawal) }}</span>

                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền cần nạp :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(sumTotalDetail(userDataAddNew)) }}</span>

                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền cần rút :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(sumTotalDetailDrawal(userDataAddNew)) }}</span>

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
                                            <p> Bạn chắc chắn muốn xoá Thẻ <br> ID : {{ idDelete }} </p>
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
                    <div>
                        <div class="modal fade" id="modal-reDone" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Cho phép sửa lại </h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row text-center">
                                            <p> Bạn chắc chắn cho phép sửa lại GD<br> ID : {{ idReDone }} </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closeModalReDone"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary" @click="actionReDone()">Đồng
                                            ý</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Card -->

                    <div>
                        <div class="modal right fade"  id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Sửa giao dịch</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Khách hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <div>{{ userData.name }}</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">STT <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" v-model="userData.stt" name="name" class="form-control"
                                                    placeholder="stt" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.name" name="name" class="form-control"
                                                    placeholder="Nhập tên gd" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">NV SHIP <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.user_fee_id" :options="globalStore.listUser" 
                                                    label="name" :reduce="customer => customer.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tiền Rút <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userData.money_withdrawal"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí ship <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'" v-model:value="userData.fee_ship"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí nhân viên <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'" v-model:value="userData.fee_user"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [%] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userData.fee_customer"
                                                    :options="{ precision: 2, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [vnđ] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userData.fee_money_customer"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <flat-pickr v-model="userData.datetime" :config="config"/>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-header">
                                            <div class="form-group row" >
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết đáo hạn</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div v-for="(detail, loop) in userData.details">
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="text-center">Lần: <span class="loop ">{{ loop + 1 }}</span>
                                                        &nbsp;
                                                        <button type="button" @click="copyText('NTDH '+userData.id+' '+userData.customer_id)">NTDH {{userData.id}} {{userData.customer_id}} </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-right"> 
                                                    <button class="fa fa-trash operation-icon text-danger" title="Xoá Thẻ KH" @click="removeItem(userData,detail)"></button>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">POS<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="detail.pos_id" :options="globalStore.listPos"
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Nạp<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.money"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Rút<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.money_drawal"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Lô.Bill<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat :class="'form-control'"
                                                                    v-model:value="detail.group_bill"
                                                                    :options="{ precision: 3, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                        </div>
                                        <div class="col-md-8" >
                                            <label class="btn btn-sm btn-primary float-left add-detail" type="button "
                                                @click="addItem()">Thêm</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="modal-footer justify-content-between">
                                        
                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền Đáo hạn :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(userData.money_withdrawal) }}</span>

                                            </div>

                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền cần nạp :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(sumTotalDetail(userData)) }}</span>

                                            </div>
                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền cần rút :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(sumTotalDetailDrawal(userData)) }}</span>

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
                    <!-- Show Card -->

                    <div>
                        <div class="modal right fade"  id="modal-show"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Xem giao dịch</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                                            @click="isPopupEdit = false">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Khách hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <div>{{ userDataShow.name }}</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">STT <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disabled type="number" v-model="userDataShow.stt" name="name" class="form-control"
                                                    placeholder="stt" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly disabled type="text" v-model="userDataShow.name" name="name" class="form-control"
                                                    placeholder="Nhập tên gd" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">NV SHIP <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select readonly disabled v-model="userDataShow.user_fee_id" :options="globalStore.listUser" 
                                                    label="name" :reduce="customer => customer.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tiền Rút <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userDataShow.money_withdrawal"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí ship <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'" v-model:value="userDataShow.fee_ship"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí nhân viên <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'" v-model:value="userDataShow.fee_user"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [%] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userDataShow.fee_customer"
                                                    :options="{ precision: 2, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Phí KH [vnđ] <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                    v-model:value="userDataShow.fee_money_customer"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">

                                                <flat-pickr v-model="userDataShow.datetime" :config="config"/>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-header">
                                            <div class="form-group row" >
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết đáo hạn</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div v-for="(detail, loop) in userDataShow.details">
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="text-center">Lần: <span class="loop ">{{ loop + 1 }} &nbsp;</span>
                                                        <button type="button"  @click="copyText('NTDH '+userDataShow.id+' '+userDataShow.customer_id)"  >NTDH {{userDataShow.id}} {{userDataShow.customer_id}} </button>

                                                    </div>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">POS<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select readonly disabled v-model="detail.pos_id" :options="globalStore.listPos" 
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Nạp<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                                    v-model:value="detail.money"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Rút<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                                    v-model:value="detail.money_drawal"
                                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Lô.Bill<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <VueNumberFormat readonly disabled :class="'form-control'"
                                                                    v-model:value="detail.group_bill"
                                                                    :options="{ precision: 3, prefix: '', suffix: '', decimal: '.', thousand: '', acceptNegative: false, isInteger: false }">
                                                                </VueNumberFormat>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="modal-footer justify-content-between">
                                        
                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền Đáo hạn :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(userDataShow.money_withdrawal) }}</span>

                                            </div>

                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền cần nạp :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(sumTotalDetail(userDataShow)) }}</span>

                                            </div>
                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền cần rút :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(sumTotalDetailDrawal(userDataShow)) }}</span>

                                            </div>
                                            
                                    </div>
                                   
                                    <div class="modal-footer justify-content-between" v-if="!userDataShow.isDone">
                                        <button type="button" class="btn btn-default CloseModalShow"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button" class="btn btn-primary"  @click="actionVerify(userDataShow.id)">Xác nhận GIAO DỊCH</button>
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
import { useCustomerListStore } from '@/pages/customers/useCustomerListStore'
import { useWithDrawalListStore } from '@/pages/withDrawal/useWithDrawalListStore'

import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import { useRoute } from 'vue-router';
import moment from "moment";

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

import jquery from 'jquery';

const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'm/j/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

const dateTime = (value) => {
    return moment(value).utc().format("DD/MM/YYYY");
}

const route = useRoute();

const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};

const copyText = (text) => {
        const storage = document.createElement('input');
        storage.setAttribute('type', 'text')
        // storage.setAttribute('value', text)
        document.body.appendChild(storage)
        storage.value = text;
        storage.select();
        storage.setSelectionRange(0, 99999);

        storage.focus();
        try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        alert('Copied ' + text);
        } catch (err) {
        alert('Oops, unable to copy');
        }
        /* unselect the range */
        document.body.removeChild(storage);
        window.getSelection().removeAllRanges()
};


const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "Ngày GD", value: "created_at" },
    { text: "KH", value: "customer.name" },
    { text: "Số tiền rút", value: "money_withdrawal" },
    { text: "Phí KH(%)", value: "fee_customer" },
    { text: "Shipto", value: "user_belongto.name" },
    { text: "Người tạo", value: "user.name" },
    { text: "Operation", value: "operation" },
];

const items = ref([])
const allBranchs = ref([])
const serverOptions = ref<ServerOptions>({
    page: 1,
    rowsPerPage: 20,
    sortBy: 'id',
    sortType: 'desc',
});
const objDefault = {
    'name': '',
    'stt': '',
    'customer_card_id': '',
    'user_fee_id': '',
    'money_withdrawal': 0,
    'fee_customer': 0,
    'transfer': false,
    'fee_money_customer': 0,
    'datetime': new Date(),
    'note': '',
    'fee_ship': 0,
    'fee_user': 0,
    'customer_id': 0,
    'details': []
}

const loading = ref(false)
const serverItemsLength = ref(0);
const reRender = ref(0);
const isPopupDelete = ref(false);
const idDelete = ref(0);
const idReDone = ref(0);
const isPopupEdit = ref(false);
const idEdit = ref(0);
const idShow = ref(0);
const userData = ref({ ...objDefault })
const userDataShow = ref({ ...objDefault })
const userDataAddNew = ref({ ...objDefault })
userDataAddNew.value.customer_id = parseInt(route.query.customer_id ?? 0);
const useCardStore = useCustomerCardListStore()
const useWithDrawalStore = useWithDrawalListStore()

const useCustomerStore = useCustomerListStore()
const customerSearch = ref()

const globalStore = useGlobalStore()
const searchValue = ref()
const showDetail = dataInfo => {
    userData.value = dataInfo
}
const provinceId = ref('')

const listCards = ref()
const formatDate = (date) => {
    const tzOffset = date.getTimezoneOffset() * 60 * 1000
    return new Date(date - tzOffset).toISOString().split('T')[0]
}
const askDelete = dataInfo => {
    idDelete.value = dataInfo.id
}
const asReDone = dataInfo => {
    idReDone.value = dataInfo.id
}
const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    useWithDrawalStore.fetchWithDrawal(idEdit.value).then(({ data }) => {
        userData.value = data.withdrawal
    }).catch(error => {
        toast.error(error.message);
    })
}
const askShow = dataInfo => {
    idShow.value = dataInfo.id
    useWithDrawalStore.fetchWithDrawal(idShow.value).then(({ data }) => {
        userDataShow.value = data.withdrawal
    }).catch(error => {
        toast.error(error.message);
    })
}

const selectedCustomer = (value) => {
    if (timer.value) {
        clearTimeout(timer.value);
        timer.value = 0;
    }
    timer.value = setTimeout(() => {
        useCardStore.fetchAllCustomerCards(ref({ search: value.id }).value).then(({ data }) => {
            listCards.value = data.customer_cards
        })
    }, 500);
    userDataAddNew.value.name = value.name_phone
    userData.value.name = value.name_phone
};

const fetchCustomer = (query) => {
    if (query) {
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            useCustomerStore.fetchAllCustomers(ref({ query: query }).value).then(({ data }) => {
                customerSearch.value = data.customers
            })
        }, 500);
    }
}
const fetchCustomerFirst = (query) => {
    if(userDataAddNew.value.customer_id) {
        useCustomerStore.fetchAllCustomers(ref({ customer_id: route.query.customer_id}).value).then(({ data }) => {
            customerSearch.value = data.customers
            useCardStore.fetchAllCustomerCards(ref({ search: userDataAddNew.value.customer_id }).value).then(({ data }) => {
                listCards.value = data.customer_cards
                userDataAddNew.value.name =  (customerSearch.value.find((customer) => customer.id == userDataAddNew.value.customer_id).name_phone)
            })

        })
    }
}

const fetchAll = () => {
    loading.value = true;
    useWithDrawalStore.fetchWithDrawals(serverOptions.value, searchValue.value, route.query.customer_id).then(({ data }) => {
        serverItemsLength.value = data.withdrawals.total
        loading.value = false;
        items.value = data.withdrawals.data

    }).catch(error => {
        toast.error(error.message);
    })

};

const actionVerify = (verifyId) => {
    loading.value = false;
    useWithDrawalStore.verifyData(verifyId).then(response => {
        toast.success('Xác nhận thành công');
        loading.value = true;
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}

const actionReDone = () => {
    loading.value = false;
    useWithDrawalStore.reDoneData(idReDone.value).then(response => {
        idReDone.value = 0
        toast.success('Cho phép sửa lại thành công');
        loading.value = true;
        jquery('.closeModalReDone').click()
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}
const actionDelete = () => {
    loading.value = false;
    useWithDrawalStore.deleteData(idDelete.value).then(response => {
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
    useWithDrawalStore.updateData(userData.value).then(response => {
        loading.value = true
        toast.success(response.data.message)
        jquery('.CloseModalEdit').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const actionCreate = () => {
    loading.value = false;
    useWithDrawalStore.addWithDrawal(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.message)
        jquery('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const addItem = () => {
    userData.value.details.push({ money_drawal: 0, money: '', group_bill: '', pos_id: '' })
}
const addItemAddNew = () => {
    userDataAddNew.value.details.push({ money_drawal: 0, money: '', group_bill: '', pos_id: '' })
}

const removeItem =(obj,loop) => {
    obj.details.splice(obj.details.indexOf(loop),1)
}
const sumTotalDetail = (data) => {
    let total = 0;
    if(data.details.length > 0) {
         data.details.forEach(element => {
            total = total + element.money;
        });
    }
    
    return data.money_withdrawal - total;
}

const sumTotalDetailDrawal = (data) => {
    let total = 0
    data.details.forEach(element => {
        total += element.money
    });
    let total_drawal = 0
    data.details.forEach(element => {
        total_drawal += element.money_drawal
    });
    return total  - total_drawal
}

fetchAll()
globalStore.fetchListBank()
globalStore.fetchListUser()
globalStore.fetchListPos()
const timer = ref(500)


watch(serverOptions, (value, value2) => {
    if (value != value2) {
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
    if (timer.value) {
        clearTimeout(timer.value);
        timer.value = 0;
    }
    timer.value = setTimeout(() => {
        fetchAll()
    }, 500);
}, { deep: true })

const userDataAddNewWatch = ref({ ...objDefault })
watch(userDataAddNew, (value) => {
    if (value.money_withdrawal != userDataAddNewWatch.value.money_withdrawal
        || value.fee_customer != userDataAddNewWatch.value.fee_customer
        || value.fee_ship != userDataAddNewWatch.value.fee_ship) {
        userDataAddNewWatch.value.fee_ship = value.fee_ship
        userDataAddNewWatch.value.money_withdrawal = value.money_withdrawal
        userDataAddNewWatch.value.fee_customer = value.fee_customer
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            userDataAddNew.value.fee_money_customer = (value.money_withdrawal * value.fee_customer / 100) + value.fee_ship;
            const rest = value.fee_money_customer % 1000;
            if (rest > 500) {
                userDataAddNew.value.fee_money_customer = userDataAddNew.value.fee_money_customer - rest + 1000;
            } else {
                userDataAddNew.value.fee_money_customer = userDataAddNew.value.fee_money_customer - rest;

            }
        }, 500);
    }
}, { deep: true }
);


watch(userData, (value) => {
    if (value.money_withdrawal != userDataAddNewWatch.value.money_withdrawal
        || value.fee_customer != userDataAddNewWatch.value.fee_customer
        || value.fee_ship != userDataAddNewWatch.value.fee_ship) {
        userDataAddNewWatch.value.fee_ship = value.fee_ship
        userDataAddNewWatch.value.money_withdrawal = value.money_withdrawal
        userDataAddNewWatch.value.fee_customer = value.fee_customer
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            userDataAddNew.value.fee_money_customer = (value.money_withdrawal * value.fee_customer / 100) + value.fee_ship;
            const rest = value.fee_money_customer % 1000;
            if (rest > 500) {
                userDataAddNew.value.fee_money_customer = userDataAddNew.value.fee_money_customer - rest + 1000;
            } else {
                userDataAddNew.value.fee_money_customer = userDataAddNew.value.fee_money_customer - rest;

            }
        }, 500);
    }
}, { deep: true }
);

</script>