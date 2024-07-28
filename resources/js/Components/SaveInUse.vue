<template>
    <div class="w-full">
        <InputLabel class="font-black text-5xl">Start At:</InputLabel>
        <TextInput type="datetime-local" class="w-full font-black text-5xl mt-4" v-model="formData.start"></TextInput>
        <button class="w-full bg-green-400 p-4 mt-4 rounded-md font-black text-5xl">START</button>
    </div>
</template>

<script setup>
import InputLabel from './InputLabel.vue';
import TextInput from './TextInput.vue';
</script>

<style lang="scss" scoped></style>