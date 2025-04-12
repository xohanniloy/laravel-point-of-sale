<script setup>
import { usePage, useForm, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
    position: "top-right",
    duration: 3000,
});

const page = usePage();
const profile = page.props.user;
const form = useForm({
    name: '',
    email: '',
    phone: ''
});
form.name = profile.name;
form.email = profile.email;
form.phone = profile.phone;
const submit = () => {
    if (form.name == '' || form.email == '' || form.phone == '') {
        toaster.error("Please enter all fields");
        return;
    }else {
        form.post("/update-profile", {
            onSuccess: () => {
                if (page.props.flash?.status === true) {
                    toaster.success(page.props.flash?.message || 'Profile updated successfully');
                    router.get("/profile");
                } else {
                    toaster.error(page.props.flash?.message || "Profile update failed. Please check your credentials.");
                }
            }
        });
    }
}
</script>

<template>
    <!-- start content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card animated fadeIn w-100 p-3">
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <h4>Profile Update</h4>
                            <hr />
                            <div class="container-fluid m-0 p-0">
                                <div class="row m-0 p-0">
                                    <div class="col-md-4 p-2">
                                        <label>Name</label>
                                        <input v-model="form.name" id="name" placeholder="First Name" class="form-control" type="text" />
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <label>Email Address</label>
                                        <input v-model="form.email" id="email" placeholder="User Email" class="form-control" type="email" disabled />
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <label>Mobile Number</label>
                                        <input v-model="form.phone" id="mobile" placeholder="Mobile" class="form-control" type="mobile" />
                                    </div>
                                </div>
                                <div class="row m-0 p-0">
                                    <div class="col-md-4 p-2">
                                        <button type="submit" class="btn mt-3 w-100  btn-success">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end content -->
</template>