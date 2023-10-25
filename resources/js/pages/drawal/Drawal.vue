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
                            <button class="card-header-action" data-bs-toggle="modal" data-bs-target="#modal-add-new" @click="fetchCustomerFirst">
                                <i class="fa fa-plus"></i> Thêm</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <EasyDataTable v-model:server-options="serverOptions" :server-items-length="serverItemsLength"
                        :loading="loading" :headers="headers" :items="items" :key="reRender" buttons-pagination
                        table-class-name="customize-table">

                        <template #item-id="item">
                            <div class="operation-wrapper">
                                <div>  <i title="Chỉnh sửa text-danger cursor-pointer"  @click="askShow(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-show">{{ (item.id) }}</i></div>
                            </div>
                        </template>

                        <template #item-money_drawal="item">
                            <div class="operation-wrapper">
                                <div>{{ formatPrice(item.money_drawal) }}</div>

                            </div>
                        </template>

                        <template #item-created_at="item">
                            <div class="operation-wrapper">
                                <div>{{ dateTime(item.created_at) }}</div>

                            </div>
                        </template>

                        <template #item-user="item">
                            <div class="operation-wrapper">
                                <div v-if="item.user">{{ (item.user.name) }}</div>

                            </div>
                        </template>

                        <template #item-operation="item">
                            <div class="operation-wrapper">
                                <i class="fa fa-eye operation-icon" title="Chỉnh sửa"  @click="askShow(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-show"></i>

                                <i class="fa fa-edit operation-icon" title="Chỉnh sửa" v-if="!item.isDone" @click="askEdit(item)"
                                    data-bs-toggle="modal" data-bs-target="#modal-edit"></i>
                                <i class="fa fa-trash operation-icon text-danger" v-if="!item.isDone && userLogin.type=='admin'" @click="askDelete(item)" 
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
                                                <label for="name" class="col-form-label">Khách hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.customer_id" :options="customerSearch" :filterable="false"
                                                    label="name_phone" :reduce="customer => customer.id"
                                                    @search="fetchCustomer" @search:focus="fetchCustomer" @option:selected="selectedCustomer"></v-select>
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
                                                <label for="name" class="col-form-label">Số tiền Rút <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat :class="'form-control'"
                                                    v-model:value="userDataAddNew.money_drawal"
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
                                                <label for="name" class="col-form-label">Ngân hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataAddNew.bank_id" :options="globalStore.listBank"  
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tài khoản <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.bank_code" name="bank_code"
                                                    class="form-control" placeholder="Số tài khoản" maxlength="100"
                                                    required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userDataAddNew.bank_customer_name"
                                                    name="bank_customer_name" class="form-control"
                                                    placeholder="Tên chủ tài khoản" maxlength="100" required />
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
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết rút tiền</label>
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
                                                <label for="name" class="col-md-4 col-form-label">Thẻ<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="detail.customer_card_id" :options="listCards"  
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

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
                                                        <label for="name" class="col-md-4 col-form-label">Rút<span
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
                                            tiền sẽ Chuyển :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(userDataAddNew.money_drawal-userDataAddNew.fee_money_customer) }}</span>
                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền sẽ rút :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(userDataAddNew.money_drawal) }}</span>
                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền cần rút :<br>{{ sumTotalDetail(userData)  }}<br>
                                            <span  class="total_money_transfer"></span>

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
                                        <h4 class="modal-title">Xoá GD </h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row text-center">
                                            <p> Bạn chắc chắn muốn xoá GD <br> ID : {{ idDelete }} </p>
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
                        <div class="modal right fade" id="modal-edit"
                            aria-hidden="true">
                            <div class="modal-dialog" v-if="userData">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Sửa giao dịch</h4>
                                        &nbsp;
                                        <button type="button" @click="copyText('CKRT '+userData.id+' '+userData.customer_id)"  >CKRT {{userData.id}} {{userData.customer_id}} </button>
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
                                                    v-model:value="userData.money_drawal"
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
                                                <label for="name" class="col-form-label">Ngân hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userData.bank_id" :options="globalStore.listBank"  
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tài khoản <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.bank_code" name="bank_code"
                                                    class="form-control" placeholder="Số tài khoản" maxlength="100"
                                                    required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" v-model="userData.bank_customer_name"
                                                    name="bank_customer_name" class="form-control"
                                                    placeholder="Tên chủ tài khoản" maxlength="100" required />
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
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết rút tiền</label>
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
                                                        <button type="button" @click="copyText('CKRT '+userData.id+' '+userData.customer_id)"  >CKRT {{userData.id}} {{userData.customer_id}} </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-right"> 
                                                    <button class="fa fa-trash operation-icon text-danger" title="Xoá Thẻ KH" @click="removeItem(userData,detail)"></button>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">Thẻ<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="detail.customer_card_id" :options="listCards"  :filterable="false"
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">POS<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select v-model="detail.pos_id" :options="globalStore.listPos"  :filterable="false"
                                                            label="name" :reduce="card => card.id"></v-select>
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
                                                        <label for="name" class="col-md-4 col-form-label">Lô<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <input  type="number" v-model="detail.lo" name="name" class="form-control"
                                                                    placeholder="Nhập lô" maxlength="100" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Bill<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <input type="number"  v-model="detail.bill" name="name" class="form-control"
                                                                    placeholder="Nhập Bill" maxlength="100" required />
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
                                            tiền sẽ Chuyển :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(userData.money_drawal-userData.fee_money_customer) }}</span>
                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền sẽ rút :<br>
                                            <span  class="total_money_transfer">{{ formatPrice(userData.money_drawal) }}</span>
                                        </div>

                                        <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                            tiền cần rút :<br>{{ sumTotalDetail(userData)  }}<br>
                                            <span  class="total_money_transfer"></span>

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
                    <!-- show Card -->

                    <div>
                        <div class="modal right fade" id="modal-show"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" v-if="userData">
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
                                                <input readonly type="number" v-model="userDataShow.stt" name="name" class="form-control"
                                                    placeholder="stt" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên GD <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input readonly type="text" v-model="userDataShow.name" name="name" class="form-control"
                                                    placeholder="Nhập tên gd" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">NV SHIP <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select v-model="userDataShow.user_fee_id" :options="globalStore.listUser"  
                                                    label="name" :reduce="customer => customer.id"></v-select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tiền Rút <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <VueNumberFormat readonly  :class="'form-control'"
                                                    v-model:value="userDataShow.money_drawal"
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
                                                <VueNumberFormat readonly :class="'form-control'" v-model:value="userDataShow.fee_ship"
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
                                                <VueNumberFormat readonly :class="'form-control'" v-model:value="userDataShow.fee_user"
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
                                                <VueNumberFormat readonly :class="'form-control'"
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
                                                <VueNumberFormat readonly :class="'form-control'"
                                                    v-model:value="userDataShow.fee_money_customer"
                                                    :options="{ precision: 0, prefix: '', suffix: '', decimal: '.', thousand: ',', acceptNegative: false, isInteger: false }">
                                                </VueNumberFormat>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngân hàng <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <v-select disable v-model="userDataShow.bank_id" :options="globalStore.listBank"  
                                                    label="shortName" :reduce="bank => bank.id"></v-select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Số tài khoản <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="btn btn-info"  @click="copyText(userDataShow.bank_code)"   > {{userDataShow.bank_code}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Nội dung <span
                                                    class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="btn btn-info"  type="button"  @click="copyText('CKRT '+userDataShow.id+' '+userDataShow.customer_id)"   >CKRT {{userDataShow.id}} {{userDataShow.customer_id}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Tên chủ TK <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" readonly v-model="userDataShow.bank_customer_name"
                                                    name="bank_customer_name" class="form-control"
                                                    placeholder="Tên chủ tài khoản" maxlength="100" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="name" class="col-form-label">Ngày <span
                                                                class="text-danger">(*)</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <flat-pickr v-model="userDataShow.datetime" :config="config" readonly disabled/>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-header">
                                            <div class="form-group row" >
                                                <label for="name" class="col-md-4 col-form-label">Chi tiết rút tiền</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div v-for="(detail, loop) in userDataShow.details">
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="text-center">Lần: <span class="loop ">{{ loop + 1 }}</span> &nbsp;
                                                        <button type="button"  @click="copyText('CKRT '+userDataShow.id+' '+userDataShow.customer_id)"   >CKRT {{userDataShow.id}} {{userDataShow.customer_id}} </button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">Thẻ<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select readonly disabled v-model="detail.customer_card_id" :options="listCards"  :filterable="false"
                                                            label="name" :reduce="card => card.id"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label">POS<span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <v-select readonly disabled v-model="detail.pos_id" :options="globalStore.listPos"  :filterable="false"
                                                            label="name" :reduce="card => card.id"></v-select>
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
                                                        <label for="name" class="col-md-4 col-form-label">Lô<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <input readonly type="text" v-model="detail.lo" name="name" class="form-control"
                                                                    placeholder="Nhập lô" maxlength="100" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label for="name" class="col-md-4 col-form-label">Bill<span
                                                                class="text-danger">(*)</span></label>
                                                        <div class="col-md-8">
                                                            <div>
                                                                <input type="number" readonly v-model="detail.bill" name="name" class="form-control"
                                                                    placeholder="Nhập Bill" maxlength="100" required />
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
                                                tiền sẽ Chuyển :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(userDataShow.money_drawal-userDataShow.fee_money_customer) }}</span>
                                            </div>

                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền sẽ rút :<br>
                                                <span  class="total_money_transfer">{{ formatPrice(userDataShow.money_drawal) }}</span>
                                            </div>

                                            <div class="btn btn-sm btn-danger float-right my-2 px-2 mx-2" >Số
                                                tiền cần rút :<br>{{ sumTotalDetail(userDataShow)  }}<br>
                                                <span  class="total_money_transfer"></span>

                                            </div>
                                    </div>
                                    
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-success CloseModalCreate"
                                            data-bs-dismiss="modal">Huỷ</button>
                                        <button type="button"  @click="copyText('CKRT '+userDataShow.id+' '+userDataShow.customer_id)">CKRT {{userDataShow.id}} {{userDataShow.customer_id}} </button>
                                        <input id="testing-code" value='' type ='hidden'>
                                    </div>
                                    <div class="modal-footer justify-content-between" v-if="!userDataShow.isDone">
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
import { useDrawalListStore } from '@/pages/drawal/useDrawalListStore'

import { toast } from 'vue3-toastify';
import { useGlobalStore } from '@/store/globalStore'
import { useRoute } from 'vue-router';
import moment from "moment";

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const config = ref({
    wrap: true, // set wrap to true only when using 'input-group'
    altFormat: 'm/j/Y',
    altInput: true,
    dateFormat: 'Y-m-d',
});

const dateTime = (value) => {
    return moment(value).utc().format("DD/MM/YYYY");
}
const userLogin  = JSON.parse(localStorage.getItem('user'))
const route = useRoute();

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

const formatPrice = (value) => {
    const val = (value / 1).toFixed(0).replace(',', '.')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
};
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

const headers: Header[] = [
    { text: "ID", value: "id", sortable: true },
    { text: "Ngày GD", value: "created_at" },
    { text: "KH", value: "customer.name" },
    { text: "Số tiền rút", value: "money_drawal" },
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
    'user_fee_id': '',
    'money_drawal': 0,
    'fee_customer': 0,
    'transfer': false,
    'fee_money_customer':0,
    'bank_id': ref(),
    'bank_code': ref(),
    'bank_customer_name': ref(),
    'datetime': new Date(),
    'note': '',
    'fee_ship': 0,
    'fee_user': 0,
    'customer_id': route.query.customer_id??0,
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
const useDrawalStore = useDrawalListStore()

const useCustomerStore = useCustomerListStore()
const customerSearch = ref()

const globalStore = useGlobalStore()
const searchValue = ref()
const showDetail = dataInfo => {
    userData.value = dataInfo
}

const listCards = ref()

const askDelete = dataInfo => {
    idDelete.value = dataInfo.id
}
const asReDone = dataInfo => {
    idReDone.value = dataInfo.id
}
const askEdit = dataInfo => {
    isPopupEdit.value = true
    idEdit.value = dataInfo.id
    useDrawalStore.fetchDrawal(idEdit.value).then(({ data }) => {
        userData.value = data.drawal
        useCardStore.fetchAllCustomerCards(ref({ search: userData.value.customer_id }).value).then(({ data }) => {
            listCards.value = data.customer_cards
        })
    }).catch(error => {
        toast.error(error.message);
    })
}
const askShow = dataInfo => {
    idShow.value = dataInfo.id
    useDrawalStore.fetchDrawal(idShow.value).then(({ data }) => {
        userDataShow.value = data.drawal
        useCardStore.fetchAllCustomerCards(ref({ search: userData.value.customer_id }).value).then(({ data }) => {
            listCards.value = data.customer_cards
        })
    }).catch(error => {
        toast.error(error.message);
    })
}

const fetchCustomer = (query) => {
    if (query) {
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            useCustomerStore.fetchAllCustomers(ref({ customer_id: route.query.customer_id,query:query }).value).then(({ data }) => {
                customerSearch.value = data.customers
                if(userDataAddNew.value.customer_id) {
                    userDataAddNew.value.name =  (customerSearch.value.find((customer) => customer.id == userDataAddNew.value.customer_id).name_phone);
                }
            })
        }, 500);
    }
}

const fetchCustomerFirst = () => {
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
    useDrawalStore.fetchDrawals(serverOptions.value, searchValue.value, route.query.customer_id).then(({ data }) => {
        serverItemsLength.value = data.drawals.total
        loading.value = false;
        items.value = data.drawals.data

    }).catch(error => {
        toast.error(error.message);
    })

};

const actionVerify = (verifyId) => {
    loading.value = false;
    useDrawalStore.verifyData(verifyId).then(response => {
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
    useDrawalStore.reDoneData(idReDone.value).then(response => {
        idReDone.value = 0
        toast.success('Cho phép sửa lại thành công');
        loading.value = true;
        $('.closeModalReDone').click()
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}
const actionDelete = () => {
    loading.value = false;
    useDrawalStore.deleteData(idDelete.value).then(response => {
        idDelete.value = 0
        toast.success('Xóa thành công');
        loading.value = true;
        $('.closeModalDelete').click()
        fetchAll()

    }).catch(({ response }) => {
        isPopupDelete.value = false
        toast.error(response.data.message);
    })

}

const actionUpdate = () => {
    loading.value = false;
    useDrawalStore.updateData(userData.value).then(response => {
        loading.value = true
        toast.success(response.data.message)
        $('.CloseModalEdit').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const actionCreate = () => {
    loading.value = false;
    useDrawalStore.addDrawal(userDataAddNew.value).then(response => {
        loading.value = true
        toast.success(response.data.message)
        $('.CloseModalCreate').click()
        fetchAll()
    }).catch(({ response }) => {
        toast.error(response.data.message);
    })
}
const addItem = () => {
    userData.value.details.push({ customer_card_id: 0, money: '', group_bill: '', pos_id: '' })
}
const addItemAddNew = () => {
    userDataAddNew.value.details.push({ customer_card_id: 0, money: '', group_bill: '', pos_id: '' })
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
    return formatPrice(data.money_drawal - total )
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
// branchssudo systemctl enable nginx

const userDataAddNewWatch = ref({ ...objDefault })
watch(userDataAddNew, (value) => {
    if (value.money_drawal != userDataAddNewWatch.value.money_drawal
        || value.fee_customer != userDataAddNewWatch.value.fee_customer
        || value.fee_ship != userDataAddNewWatch.value.fee_ship) {
        userDataAddNewWatch.value.fee_ship = value.fee_ship
        userDataAddNewWatch.value.money_drawal = value.money_drawal
        userDataAddNewWatch.value.fee_customer = value.fee_customer
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            userDataAddNew.value.fee_money_customer = (value.money_drawal * value.fee_customer / 100) + value.fee_ship;
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
    if (value.money_drawal != userDataAddNewWatch.value.money_drawal
        || value.fee_customer != userDataAddNewWatch.value.fee_customer
        || value.fee_ship != userDataAddNewWatch.value.fee_ship) {
        userDataAddNewWatch.value.fee_ship = value.fee_ship
        userDataAddNewWatch.value.money_drawal = value.money_drawal
        userDataAddNewWatch.value.fee_customer = value.fee_customer
        if (timer.value) {
            clearTimeout(timer.value);
            timer.value = 0;
        }
        timer.value = setTimeout(() => {
            userDataAddNew.value.fee_money_customer = (value.money_drawal * value.fee_customer / 100) + value.fee_ship;
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