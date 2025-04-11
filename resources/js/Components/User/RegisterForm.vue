<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});
const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    cpassword: '',
});
const page = usePage();
const submitRegister = () => {
    if (!form.name || form.name.trim() === '' ||
        !form.email || form.email.trim() === '' ||
        !form.phone || form.phone.trim() === '' ||  // Corrected from form.mobile to form.phone
        !form.password || form.password.trim() === '' ||
        !form.cpassword || form.cpassword.trim() === '') {
        toaster.error("Please enter all fields");
        return;
    }

    // Ensure passwords match
    if (form.password !== form.cpassword) {
        toaster.error("Passwords do not match");
        return;
    }

    form.post("/user-registration", {
        onSuccess: () => {
            if (page.props.flash?.status === true) {
                toaster.success('Registration successful');
                router.get("/login");
            } else {
                toaster.error(page.props.flash?.message || "Registration failed. Please check your credentials.");
            }
        },
        onError: (errors) => {
            toaster.error(errors.message || "Registration failed. Please check your credentials.");
        }
    });
}

</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-10 center-screen">
                <div class="card animated fadeIn w-50 p-3">
                    <form @submit.prevent="submitRegister">
                        <div class="card-body">
                            <h4>Sign Up</h4>
                            <hr />
                            <div class="container-fluid m-0 p-0">
                                <div class="row m-0 p-0">
                                    <div class="col-12 p-2">
                                        <label>Name</label>
                                        <input v-model="form.name" id="name" placeholder="First Name"
                                            class="form-control" type="text" />
                                    </div>
                                    <div class="col-12 p-2">
                                        <label>Email Address</label>
                                        <input v-model="form.email" id="email" placeholder="User Email"
                                            class="form-control" type="email" />
                                    </div>
                                    <div class="col-12 p-2">
                                        <label>Mobile Number</label>
                                        <!-- <input v-model="form.phone" id="mobile" placeholder="Mobile"
                                            class="form-control" type="mobile" /> -->
                                        <input v-model="form.phone" id="mobile" placeholder="Mobile"
                                            class="form-control" type="tel" />

                                    </div>
                                    <div class="col-12 p-2">
                                        <label>Password</label>
                                        <input v-model="form.password" id="password" placeholder="User Password"
                                            class="form-control" type="password" />
                                    </div>
                                    <div class="col-12 p-2">
                                        <label>Confirm Password</label>
                                        <input v-model="form.cpassword" id="cpassword"
                                            placeholder="User Confirm Password" class="form-control" type="password" />
                                    </div>
                                </div>
                                <div class="row m-0 p-0">
                                    <div class="col-md-4 p-2">
                                        <button type="submit" class="btn mt-3 w-100  btn-success">Complete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>