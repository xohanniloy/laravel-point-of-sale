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
    name: '',
    email: '', 
    phone: '',
    id: id
})
let page = usePage();

let url = "/create-customer";
let customer = page.props.customer;
if (id.value !== 0 && customer !== null) {
    url = "/update-customer";
    form.name = customer.name;
    form.email = customer.email;
    form.phone = customer.phone;
}
const submitCustomer = () => {
    if (form.name.length === 0) {
        toaster.error("Please enter customer name");
    } else {
        form.post(url, {
            onSuccess: () => {
                if (page.props.flash?.status === true) {
                    toaster.success(page.props.flash?.message || 'Customer saved successfully');
                    router.get("/customer-page");
                } else {
                    toaster.error(page.props.flash?.message || "Customer save failed. Please check your credentials.");
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
                                    <Link href="/customer-page" class="btn btn-success mx-3 btn-sm">
                                    Back
                                    </Link>
                                </div>
                                <form @submit.prevent="submitCustomer">
                                    <div class="card-body">
                                        <h4>Save Customer</h4>
                                        <input id="name" name="name" v-model="form.name" placeholder="Customer Name"
                                            class="form-control" type="text" />
                                        <br />
                                        <input id="email" name="email" v-model="form.email" placeholder="Customer Email"
                                            class="form-control" type="email" />
                                        <br />
                                        <input id="phone" name="phone" v-model="form.phone"
                                            placeholder="Customer Phone" class="form-control" type="text" />
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