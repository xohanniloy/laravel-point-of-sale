<script setup>
import { usePage, router, useForm } from '@inertiajs/vue3'
import { createToaster } from "@meforma/vue-toaster";
import { ref } from "vue";

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
let page = usePage()
const selectCustomer = ref(null);

const ProductHeader = [
    { text: "Image", value: "image" },
    { text: "Name", value: "name" },
    { text: "Quantity", value: "unit" },
    { text: "Action", value: "action" },
];

const CustomerHeader = [
    { text: "Customer", value: "name" },
    { text: "Action", value: "action" },
];
const CustomerItem = ref(page.props.customers);
const ProductItem = ref(page.props.products);
const searchCustomerValue = ref();
const searchCustomerField = ref();
const searchProductValue = ref();
const searchProductField = ref();

const vatRate = ref(0);
const flatDiscount = ref(0);
const discountPercentage = ref(0);
const total = ref(0);
const vatAmount = ref(0);
const discountAmount = ref(0);
const usePercentageDiscount = ref(false);

const selectedProduct = ref([]);

const addProductToSale = (id, image, name, price, unit) => {
    console.log('ok');

    const existingProduct = selectedProduct.value.find((product) => {
        return product.id === id;
    });
    if (existingProduct) {
        console.log(existingProduct);

        if (existingProduct.existQTY > 0) {
            existingProduct.unit++;
            existingProduct.existQTY--;
            calculateTotal();
        } else {
            toaster.error(`${name}Product out of stock`);
        }
    } else {
        if (unit > 0) {
            const newProduct = {
                id: id,
                image: image,
                name: name,
                price: price,
                unit: 1,
                existQTY: unit - 1,
                productPrice: price,
            }
            selectedProduct.value.push(newProduct);
            calculateTotal();
        } else {
            toaster.error(`${name}Product out of stock`);
        }
    }
}
</script>

<template>
    <!-- start content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Billing Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Billed To</h4>
                        <div class="shadow-sm h-100 bg-white rounded-3 p-3 mt-4">
                            <div class="row">
                                <div class="col-8">
                                    <span class="text-bold text-dark">BILLED TO</span>
                                    <p class="text-xs mx-0 my-1">Name: <span>selectedCustomer-name</span></p>
                                    <p class="text-xs mx-0 my-1">Email: <span>selectedCustomer-email</span></p>
                                    <p class="text-xs mx-0 my-1">User ID: <span>selectedCustomer-id</span></p>
                                </div>
                                <div class="col-4">
                                    <p class="text-bold mx-0 my-1 text-dark">Invoice</p>
                                    <p class="text-xs mx-0 my-1">Date: slice date</p>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <table class="table w-100">
                                        <thead>
                                            <tr class="text-xs">
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td> p name </td>
                                                <td> p unit</td>
                                                <td> p price</td>
                                                <td>
                                                    <button class="">-</button>
                                                    <button class="">+</button>
                                                    <button class="btn btn-danger btn-sm">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-bold text-xs my-1 text-dark">Total: <i
                                            class="bi bi-currency-dollar"></i> calculateTotal()</p>
                                    <p class="text-bold text-xs my-1 text-dark">VAT (vatRate%): <i
                                            class="bi bi-currency-dollar"></i> vatAmount</p>
                                    <p><button class="btn btn-info btn-sm my-1 bg-gradient-primary w-40">Apply
                                            VAT</button></p>
                                    <p><button class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            VAT</button></p>

                                    <p><span class="text-xxs">Discount Mode:</span></p>
                                    <select>
                                        <option value="false">Flat Discount</option>
                                        <option value="true">Percentage Discount</option>
                                    </select>
                                    <p class="text-bold text-xs my-1 text-dark">Discount: <i
                                            class="bi bi-currency-dollar"></i> discountAmount </p>
                                    <div>
                                        <span class="text-xxs">Flat Discount:</span>
                                        <input type="number" class="form-control w-40" min="0" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Flat Discount</button></p>
                                    </div>
                                    <div>
                                        <span class="text-xxs">Discount (%):</span>
                                        <input type="number" class="form-control w-40" min="0" max="100" step="0.25" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Percentage Discount</button></p>
                                    </div>
                                    <p><button class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            Discount</button></p>

                                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                                    <p class="text-bold text-xs my-1 text-dark">Payable: <i
                                            class="bi bi-currency-dollar"></i> payable</p>
                                    <p><button
                                            class="btn btn-success btn-sm my-3 bg-gradient-primary w-40">Confirm</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Product</h4>
                        <input v-model="searchProductValue" placeholder="Search..."
                            class="form-control mb-2 w-auto form-control-sm" type="text" />
                        <EasyDataTable buttons-paginations alternating :items="ProductItem" :headers="ProductHeader"
                            :rows-per-page="10" :search-value="searchProductValue" :seach-field="searchProductField">
                            <template #item-image="{ image }">
                                <img :src="image ? image : 'placeholder.png'" alt="Product Image" height="auto"
                                    width="40px" />
                            </template>

                            <template #item-action="{ id, image, name, price, unit }">
                                <button :class="['btn btn-sm', unit > 0 ? 'btn-success' : 'btn-danger']"
                                    :disabled="unit <= 0" @click="addProductToSale(id, image, name, price, unit)">
                                    {{ unit > 0 ? 'Add' : 'Out of Stock' }}
                                </button>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
            </div>

            <!-- Customer Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Customer</h4>
                        <input v-model="searchCustomerValue" placeholder="Search..."
                            class="form-control mb-2 w-auto form-control-sm" type="text" />
                        <EasyDataTable buttons-pagination alternating :headers="CustomerHeader" :items="CustomerItem"
                            :rows-per-page="10" :search-field="searchCustomerField" :search-value="searchCustomerValue">
                            <template #item-action="{ id, name, email }">
                                <button @click="selectCustomer = { id, name, email }"
                                    class="btn btn-success btn-sm">Add</button>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end content -->
</template>

<style scoped></style>