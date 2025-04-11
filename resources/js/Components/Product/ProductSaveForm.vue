<script setup>
import ImageUpload from './ImageUpload.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import { ref } from 'vue';

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});

const urlParams = new URLSearchParams(window.location.search);
let id = ref(parseInt(urlParams.get('id')));
const form = useForm({ name: '', price: '', unit: '', category_id: '', image: null, id: id.value || null })
let page = usePage();

const categories = ref(page.props.categories)
let url = "/create-product";
let product = page.props.product;
if (id.value !== 0 && product !== null) {
    url = "/update-product";
    form.name = product['name'];
    form.id = product['id'];
    form.price = product['price'];
    form.unit = product['unit'];
    form.category_id = product['category_id'];
    form.image = product['image'];
}
const submitProduct = () => {
    form.post(url, {
        onSuccess: () => {
            if (page.props.flash.status === true) {
                toaster.success(page.props.flash?.message || 'Product saved successfully');
                router.get("/product-page");
            } else {
                toaster.error(page.props.flash?.message || "Product save failed. Please check your data.");
            }
        }
    });
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
                                    <Link href="/product-page" class="btn btn-success mx-3 btn-sm">
                                    Back
                                    </Link>
                                </div>
                                <form @submit.prevent="submitProduct" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <h4>Save Product</h4>
                                        <input id="name" name="name" v-model="form.name" placeholder="Product Name"
                                            class="form-control" type="text" />
                                        <br />
                                        <input id="price" name="price" v-model="form.price" placeholder="Product Price"
                                            class="form-control" type="number" />
                                        <br />
                                        <input id="unit" name="unit" v-model="form.unit" placeholder="Product Unit"
                                            class="form-control" type="number" />
                                        <br />
                                        <!-- Category Dropdown -->
                                        <div>
                                            <label for="category">Select Category:</label>
                                            <select v-model="form.category_id" class="form-control" id="category">
                                                <option value="" disabled>Select a category</option>
                                                <option v-for="category in categories" :value="category.id"
                                                    :key="category.id">{{ category.name }}</option>
                                            </select>
                                        </div>
                                        <br />
                                        <div>
                                            <label for="image">Product Image:</label> <br>
                                            <ImageUpload :productImg="form.image" @image="(e) => form.image = e" />
                                        </div>
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
