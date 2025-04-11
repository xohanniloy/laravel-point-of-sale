<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const form = useForm({
    email: '',
    password: '',
});
const page = usePage();
const submit = () => {
    if (form.email.trim() === '' || form.password.trim() === '') {
        toaster.error("Please enter both Email and Password");
        return;
    }

    form.post("/user-login", {
        onSuccess: () => {
            if (page.props.flash?.status === true) {
                toaster.success('Login successful');
                router.get("/dashboard");
            } else {
                toaster.error(page.props.flash?.message || "Invalid email or password");
            }
        },
        onError: (errors) => {
            toaster.error(errors.message || "Login failed. Please check your credentials.");
        }
    });
};

</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 animated fadeIn col-lg-6 center-screen">
                <div class="card w-90  p-4">
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <h4>SIGN IN</h4>
                            <br />
                            <input v-model="form.email" id="email" placeholder="User Email" class="form-control"
                                type="email" />
                            <br />
                            <input v-model="form.password" id="password" placeholder="User Password"
                                class="form-control" type="password" />
                            <br />
                            <button type="submit" class="btn w-100 btn-success">Login</button>
                            <hr />
                            <div class="float-end mt-3">
                                <span>
                                    <Link class="text-center ms-3 h6" href="/register">Sign Up </Link>
                                    <span class="ms-1">|</span>
                                    <Link class="text-center ms-3 h6" href="/send-otp-page">Forget Password</Link>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>