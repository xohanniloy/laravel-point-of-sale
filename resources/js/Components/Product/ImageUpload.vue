<script setup>
import { ref } from 'vue';
const props = defineProps({
    productImg: String
});
const currentImg = props.productImg ? props.productImg : "placeholder.png";
const previewImage = ref(currentImg);
const emit = defineEmits(['image']);
const imageSelected = (event) => {
    const file = event.target.files[0];
    if (file) {
        previewImage.value = URL.createObjectURL(file);
        emit('image', file);
    }
}
</script>

<template>
    <div>
        <label for="image">
            <img :src="previewImage" class="img-thumbnail" height="50px" width="50px" />
        </label>
        <input @input="imageSelected($event)" type="file" name="image" id="image" />
    </div>
</template>

<style scoped></style>