@if ($logged_in_user->hasAllAccess())
    <x-utils.link class="c-subheader-nav-link" :href="route('admin.customerTransactionleted')" :text="__('Deleted Withdrawals')" />
@endif
