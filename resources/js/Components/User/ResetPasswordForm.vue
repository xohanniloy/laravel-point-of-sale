<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const form = useForm({
    password: '',
    cpassword: '',
});
const page = usePage();

const submitResetPass = () => {
    if (form.password.trim() === '' || form.cpassword.trim() === '') {
        toaster.error("Please enter all fields");
        return;
    }
    if (form.password !== form.cpassword) {
        toaster.error("Passwords do not match");
        return;
    }
    form.post("/reset-password", {
        onSuccess: () => {
            if (page.props.flash?.status === true) {
                toaster.success(page.props.flash?.message || 'Password reset successful');
                router.get("/login");
            } else {
                toaster.error(page.props.flash?.message || "Password reset failed. Please check your credentials.");
            }
        },
        onError: (errors) => {
            toaster.error(errors.message || "Password reset failed. Please check your credentials.");
        }
    });
}
</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6 center-screen">
                <div class="card animated fadeIn w-90 p-4">
                    <form @submit.prevent="submitResetPass">
                        <div class="card-body">
                            <h4>SET NEW PASSWORD</h4>
                            <br />
                            <label>New Password</label>
                            <input id="password" v-model="form.password" placeholder="New Password" class="form-control"
                                type="password" />
                            <br />
                            <label>Confirm Password</label>
                            <input id="cpassword" v-model="form.cpassword" placeholder="Confirm Password"
                                class="form-control" type="password" />
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