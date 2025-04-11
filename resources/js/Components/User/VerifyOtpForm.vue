<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const form = useForm({
    otp: '',
});
const page = usePage();

const submitOtp = () => {
    if (form.otp.trim() === '') {
        toaster.error("Please enter OTP");
        return;
    }
    form.post("/verify-otp", {
        onSuccess: () => {
            if (page.props.flash?.status === true) {
                toaster.success('Login successful');
                router.get("/reset-password");
            } else {
                toaster.error(page.props.flash?.message || "Invalid OTP");
            }
        },
        onError: (errors) => {
            toaster.error(errors.message || "OTP verification failed.");
        }
    });

}
</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6 center-screen">
                <div class="card animated fadeIn w-90  p-4">
                    <form @submit.prevent="submitOtp">
                        <div class="card-body">
                            <h4>ENTER OTP CODE</h4>
                            <br />
                            <label>4 Digit Code Here</label>
                            <input v-model="form.otp" id="otp" placeholder="Code" class="form-control" type="text" />
                            <br />
                            <button type="submit" class="btn w-100 btn-success">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>