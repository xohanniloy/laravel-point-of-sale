<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import { ref } from 'vue';

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
let page = usePage();
const Header = [
    { text: "Name", value: "name" },
    { text: "Action", value: "number" },
]
const Item = ref(page.props.categories);
const searchValue = ref();
const searchField = ref("name");

const DeleteClick = (id) => {
    let text = "Do you went to delete"
    if (confirm(text) === true) {
        router.get(`/delete-category/${id}`);
        toaster.success(page.props.flash?.message || "Category Deleted successfully");
    } else {
        text = "you canceled!";
    }
}
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12"></div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Category</h3>
                        </div>
                        <hr />
                        <div class="float-end">
                            <Link href="/category-save?id=0" class="btn btn-success mx-3 btn-sm">
                            Add Category
                            </Link>
                        </div>

                        <!-- Modal -->

                        <div>
                            <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                                v-model="searchValue">
                            <EasyDataTable show-index buttons-pagination alternating :headers="Header" :items="Item"
                                :rows-per-page="10" :search-field="searchField" :search-value="searchValue">
                                <template #item-number="{ id, name }">
                                    <Link class="btn btn-success mx-3 btn-sm" :href="`/category-save?id=${id}`">Edit
                                    </Link>
                                    <button class="btn btn-danger btn-sm" @click="DeleteClick(id)">Delete</button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>