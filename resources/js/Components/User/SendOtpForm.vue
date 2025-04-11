<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const form = useForm({
    email: '',
});
const page = usePage();
const submitOtp = () => {
    if (form.email.trim() === '') {
        toaster.error("Please enter Email");
    }

    form.post("/send-otp", {
        onSuccess: () => {
            if (page.props.flash?.status === true) {
                toaster.success('OTP sent successfully');
                router.get("/verify-otp");
            } else {
                toaster.error(page.props.flash?.message || "Invalid email or password");
            }
        },
        onError: (errors) => {
            toaster.error(errors.message || "Login failed. Please check your credentials.");
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
                            <h4>EMAIL ADDRESS</h4>
                            <br />
                            <label>Your email address</label>
                            <input v-model="form.email" id="email" placeholder="User Email" class="form-control" type="email" />

                            <button type="submit" class="btn mt-3 w-100  btn-success">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>