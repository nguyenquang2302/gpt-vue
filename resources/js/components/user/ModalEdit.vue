<script>
export default {
    name: 'ModalEdit',
    setup() {

    }
}
</script>

<template>
    <div>
        <div class="modal fade" id="modal-xl"  style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-xl" >
                <div class="modal-content" >
                    <div class="modal-header" >
                        <h4 class="modal-title">Chỉnh sửa tài khoản</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" >
                        <div class="form-group row" >
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10" >
                                <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between" >
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


