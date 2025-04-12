<script setup>
import { usePage, router, useForm } from '@inertiajs/vue3'
import { createToaster } from "@meforma/vue-toaster";
import { remove } from 'nprogress';
import { ref } from "vue";

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
let page = usePage();

// Start Customer------------
const CustomerHeader = [
    { text: "Customer", value: "name" },
    { text: "Action", value: "action" },
];
const CustomerItem = ref(page.props.customers);
const searchCustomerValue = ref();
const searchCustomerField = ref();
const selectCustomer = ref(null);
const addCustomerToSale = (Customer) => {
    selectCustomer.value = (Customer);
}
// Ended Customer------------

// Start Product------------
const ProductHeader = [
    { text: "Image", value: "image" },
    { text: "Name", value: "name" },
    { text: "Quantity", value: "unit" },
    { text: "Action", value: "action" },
];
const ProductItem = ref(page.props.products);
const searchProductValue = ref();
const searchProductField = ref();
const selectedProduct = ref([]);
const addProductToSale = (id, image, name, price, unit) => {
    const existingProduct = selectedProduct.value.find((product) => product.id === id);

    if (existingProduct) {
        if (existingProduct.existQTY > 0) {
            existingProduct.unit++;
            existingProduct.existQTY--;
            calculateTotal();
        } else {
            toaster.warning(`${name} out of stock`);
        }
    } else {
        if (unit > 0) {
            const newProduct = {
                id: id,
                image: image,
                name: name,
                price: price,
                unit: 1,
                existQTY: unit - 1
            }
            selectedProduct.value.push(newProduct);
            calculateTotal();
        } else {
            toaster.error(`${name}Product out of stock`);
        }
    }
}
// Ended Product------------

// Start Add and Remove QTY and Remove Product
const addQTY = (id) => {
    const product = selectedProduct.value.find((product) => product.id === id);

    if (!product) {
        toaster.warning("Product not found");
        return;
    }

    // Prevent adding quantity if out of stock
    if (product.existQTY <= 0) {
        toaster.warning(`${product.name} is out of stock`);
        return;
    }

    product.unit++;
    product.existQTY--;
    calculateTotal();
}
const removeQTY = (id) => {
    const product = selectedProduct.value.find((product) => product.id === id);

    if (!product) {
        toaster.warning("Product not found");
        return;
    }

    // Prevent removing more than added
    if (product.unit <= 1) {
        toaster.warning(`${product.name} quantity is already at minimum`);
        return;
    }

    product.unit--;
    product.existQTY++;
    calculateTotal();
}
const removeProductFromSale = (index) => {
    selectedProduct.value.splice(index, 1);
    calculateTotal();
}
// End Add and Remove QTY and Remove Product


const total = ref(0);


const calculateTotal = () => {
    return selectedProduct.value.reduce((total, product) => {
        return total + product.price * product.unit;
    }, 0);
}
// Start Apply and Remove Vat
const vatRate = ref(5);
const vatAmount = ref(0);
const applyVat = () => {
    vatAmount.value = (calculateTotal() * vatRate.value) / 100;
    calculateTotal();
}
const removeVat = () => {
    vatAmount.value = 0;
    vatRate.value = 0;
    calculateTotal();
}
// End Apply and Remove Vat

// start Apply and Remove Discount
const usePercentageDiscount = ref(false);
const flatDiscount = ref(0);
const percentageDiscount = ref(0);
const discountAmount = ref(0);
const applyDiscount = () => {
    if (usePercentageDiscount.value) {
        discountAmount.value = (calculateTotal() * percentageDiscount.value) / 100;
    } else {
        discountAmount.value = flatDiscount.value;
    }
    calculatePayable();

}
const removeDiscount = () => {
    discountAmount.value = 0;
    percentageDiscount.value = 0;
    flatDiscount.value = 0;
    calculatePayable();
}
let payable = ref(0);
const calculatePayable = () => {
    const totalAmount = calculateTotal();
    const discount = discountAmount.value;
    const vat = vatAmount.value;
    return payable.value = totalAmount + vat - discount;
    // return payable;
}
// End Apply and Remove Discount

const form = useForm({
    customer_id: '',
    products: '',
    vat: '',
    discount: '',
    payable: calculateTotal(),
    total: '',
});

const confirmSale = () => {
    if(!selectCustomer.value){
        toaster.warning('Please select a customer');
        return;
    }
    if(selectedProduct.value.length === 0){
        toaster.warning('Please select at least one product');
        return;
    }

    form.customer_id = selectCustomer.value.id;
    form.products = selectedProduct.value;
    form.total = total.value
    form.vat = vatAmount.value;
    form.discount = discountAmount.value;
    form.payable = payable.value;

    const calculatedTotal = calculateTotal();
    form.total = calculatedTotal;
    form.payable = payable.value;

    form.post('/create-invoice',{
        onSuccess: ()=>{
            if(page.props.flash.status === true){
                toaster.success(page.props.flash?.message || 'Invoice created successfully');
                setTimeout(() => {
                    router.get('/invoice-page');
                }, 500);
            }
            else{
                toaster.error(page.props.flash?.message || "Invoice creation failed. Please check your credentials.");
            }
        }
    });
    
};


const now = new Date();
const formattedDate = now.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
});
const formattedTime = now.toLocaleTimeString('en-US', {
    weekday: 'short',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
});

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
                                    <template v-if="selectCustomer">
                                        <p class="text-xs mx-0 my-1"><strong>Name:</strong> <span>{{
                                            selectCustomer?.name }}</span></p>
                                        <p class="text-xs mx-0 my-1"><strong>Email:</strong> <span>{{
                                            selectCustomer?.email }}</span></p>
                                        <p class="text-xs mx-0 my-1"><strong>User ID:</strong> <span>{{
                                            selectCustomer?.id }}</span></p>
                                    </template>
                                    <template v-if="!selectCustomer">
                                        <p class="text-xs mx-0 my-1"><strong>Name:</strong> <span>Your Name</span></p>
                                        <p class="text-xs mx-0 my-1"><strong>Email:</strong> <span>Your Email</span></p>
                                        <p class="text-xs mx-0 my-1"><strong>User ID:</strong> <span>Your ID</span></p>
                                    </template>
                                </div>
                                <div class="col-4">
                                    <p class="text-bold mx-0 my-1 text-dark">Invoice</p>
                                    <p class="text-xs mx-0 my-1"><strong>Time:</strong> {{ formattedTime }}</p>
                                    <p class="text-xs mx-0 my-1"><strong>Date:</strong> {{ formattedDate }}</p>
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
                                                <th>Price</th>
                                                <th>Total</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="selectedProduct.length > 0"
                                                v-for="(product, index) in selectedProduct" :key="index"
                                                class="text-left">
                                                <td> {{ product.name }} </td>
                                                <td> {{ product.unit }}</td>
                                                <td> {{ product.price }}</td>
                                                <td> {{ product.price * product.unit }}</td>
                                                <td>
                                                    <button @click="removeQTY(product.id)" class="">-</button>
                                                    <button @click="addQTY(product.id)" class="">+</button>
                                                    <button @click="removeProductFromSale(index)"
                                                        class="btn btn-danger btn-sm">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-bold text-xs my-1 text-dark">Total:
                                        <i class="bi bi-currency-dollar"></i>{{ calculateTotal() }}
                                    </p>
                                    <!-- need input for Vat -->

                                    <p class="text-bold text-xs my-1 text-dark">VAT ( {{ vatRate }}%): <i
                                            class="bi bi-currency-dollar"></i>{{
                                                vatAmount }}</p>
                                    <input class="form-control w-40" type="number" v-model="vatRate">
                                    <p class="d-flex align-items-center gap-2">
                                        <button @click="applyVat()"
                                            class="btn btn-info btn-sm my-1 bg-gradient-primary w-40">Apply VAT</button>
                                        <button @click="removeVat()"
                                            class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            VAT</button>
                                    </p>

                                    <p><span class="text-xxs">Discount Mode:</span></p>
                                    <select v-model="usePercentageDiscount">
                                        <option :value="false">Flat Discount</option>
                                        <option :value="true">Percentage Discount</option>
                                    </select>
                                    <p class="text-bold text-xs my-1 text-dark">Discount: <i
                                            class="bi bi-currency-dollar"></i>{{ discountAmount }}
                                    </p>
                                    <div v-if="!usePercentageDiscount" class="mb-2">
                                        <span class="text-xxs">Flat Discount:</span>
                                        <input v-model="flatDiscount" type="number" class="form-control w-40" min="0" />
                                        <!-- <p>
                                            <button @click="applyDiscount()"
                                                class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">
                                                Apply Flat Discount
                                            </button>
                                        </p> -->
                                    </div>
                                    <div v-else class="mb-2">
                                        <span class="text-xxs">Discount ({{ percentageDiscount }}%):</span>
                                        <input v-model="percentageDiscount" type="number" class="form-control w-40"
                                            min="0" max="100" step="0.25" />
                                        <!-- <p>
                                            <button @click="applyDiscount()"
                                                class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">
                                                Apply Percentage Discount
                                            </button>
                                        </p> -->
                                    </div>
                                    <p class="d-flex align-items-center gap-2">
                                        <button @click="applyDiscount()"
                                            class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">
                                            {{
                                                usePercentageDiscount ?
                                                    'Apply Percentage Discount' :
                                                    'ApplyFlatDiscount'
                                            }}
                                        </button>
                                        <button @click="removeDiscount()"
                                            class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">
                                            Remove Discount
                                        </button>
                                    </p>

                                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                                    <p class="text-bold text-xs my-1 text-dark">Payable: <i
                                            class="bi bi-currency-dollar"></i>{{ calculatePayable()
                                            }}</p>
                                    <p>
                                        <button @click="confirmSale()"
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
                                <button @click="addCustomerToSale({ id, name, email })"
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