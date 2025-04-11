<script setup>
import { usePage, router, Link } from '@inertiajs/vue3'
import { createToaster } from "@meforma/vue-toaster";
import { ref } from "vue";

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
let page = usePage()
const Header = [
    { text: "Image", value: "image" },
    { text: "Name", value: "name" },
    { text: "Category", value: "category.name" },
    { text: "Price", value: "price" },
    { text: "Quantity", value: "unit" },
    { text: "Action", value: "action" },
];
const Item = ref(page.props.products);
const searchValue = ref();
const searchField = ref();

const DeleteClick = (id) => {
    let text = "Do you went to delete"
    if (confirm(text) === true) {
        router.get(`/delete-product/${id}`);
        toaster.success(page.props.flash?.message || "Product Deleted successfully");
    } else {
        text = "you canceled!";
    }
}
</script>

<template>
    <div class="container-fluid">
        <main>
            <!-- start content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h3>Product</h3>
                                </div>
                                <hr />
                                <div class="float-end">
                                    <Link href="/product-save?id=0" class="btn btn-success mx-3 btn-sm">
                                    Add Product
                                    </Link>
                                </div>
                                <div>
                                    <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm"
                                        type="text" v-model="searchValue">
                                    <EasyDataTable show-index buttons-pagination alternating :headers="Header"
                                        :items="Item" :rows-per-page="10" :search-field="searchField"
                                        :search-value="searchValue">
                                        <template #item-image="{ image }" class="pt-2 pb-2">
                                            <img :src="image" :alt="image" alt="" height="auto" width="40px">
                                        </template>
                                        <template #item-action="{ id, name }">
                                            <Link class="btn btn-success mx-3 btn-sm" :href="`/product-save?id=${id}`">
                                            Edit</Link>
                                            <button class="btn btn-danger btn-sm"
                                                @click="DeleteClick(id)">Delete</button>
                                        </template>
                                    </EasyDataTable>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end content -->
        </main>
    </div>
</template>
