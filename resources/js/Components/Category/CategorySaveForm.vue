<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import { ref } from 'vue';

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const urlParams = new URLSearchParams(window.location.search);
let id = parseInt(urlParams.get('id'));
const form = useForm({
    id: id,
    name: '',
})
let page = usePage();

let url = "/create-category";
let category = page.props.category;
if (id.value !== 0 && category !== null) {
    url = "/update-category";
    form.name = category.name;
}
const submitCategory = () => {
    if (form.name.length === 0) {
        toaster.error("Please enter category name");
    } else {
        form.post(url, {
            onSuccess: () => {
                if (page.props.flash?.status === true) {
                    toaster.success(page.props.flash?.message || 'Category saved successfully');
                    router.get("/category-page");
                } else {
                    toaster.error(page.props.flash?.message || "Category save failed. Please check your credentials.");
                }
            }
        })
    }
}
</script>

<template>
    <div class="container-fluid">
        <main>
            <!-- start content -->
            <div class="container-fluid">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end">
                                    <Link href="/category-page" class="btn btn-success mx-3 btn-sm">
                                    Back
                                    </Link>
                                </div>
                                <form @submit.prevent="submitCategory">
                                    <div class="card-body">
                                        <h4>Save Category</h4>
                                        <input id="name" v-model="form.name" name="name" placeholder="Category Name"
                                            class="form-control" type="text" />
                                        <br />
                                        <button type="submit" class="btn w-100 btn-success">Save Change</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end content -->
        </main>
    </div>
</template>

<style scoped></style>