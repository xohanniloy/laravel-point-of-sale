<script setup>
import InvoiceDetails from './InvoiceDetails.vue';
import { usePage, router, Link } from '@inertiajs/vue3'
import { createToaster } from "@meforma/vue-toaster";
import { ref } from "vue";

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
let page = usePage()
const Header = [
    { text: "Name", value: "customer.name" },
    { text: "Email", value: "customer.email" },
    { text: "Phone", value: "customer.phone" },
    { text: "Total", value: "total" },
    { text: "Discount", value: "discount" },
    { text: "Vat", value: "vat" },
    { text: "Payable", value: "payable" },
    { text: "Action", value: "action" },
];
const Item = ref(page.props.invoices);
const searchValue = ref();
const searchField = ref("customer.name");
const show = ref(false);
const customer = ref({});

const showDetails = (id) => {
    show.value = !show.value;
    customer.value = Item.value.find((item) => item.id === id);
    
}
const closeModal = () => {
    show.value = !show.value
}
const DeleteClick = (id) => {
    let text = "Do you went to delete"
    if (confirm(text) === true) {
        router.get(`/delete-invoice/${id}`);
        toaster.success(page.props.flash?.message || "Invoice Deleted successfully");
    } else {
        text = "you canceled!";
    }
}
</script>

<template>
    <!-- start content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Invoice List</h3>
                        </div>
                        <hr />
                        <div>
                            <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                                v-model="searchValue">
                            <EasyDataTable show-index buttons-pagination alternating :headers="Header" :items="Item"
                                :rows-per-page="10" :search-field="searchField" :search-value="searchValue">

                                <template #item-action="{ id }">
                                    <button @click="showDetails(id)" class="viewBtn btn btn-outline-dark text-sm px-3 py-1 btn-sm mx-1">
                                        <i class="fa text-sm fa-eye"></i>
                                    </button>
                                    <button @click="DeleteClick(id)" class="btn btn-danger btn-sm">Delete</button>
                                </template>

                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal component for invoice details -->
        <InvoiceDetails v-if="show" :customer="customer" @close-modal="closeModal" />

    </div>
    <!-- end content -->
</template>
